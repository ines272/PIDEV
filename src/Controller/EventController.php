<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventTypeForm;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\User;
use Doctrine\DBAL\ArrayParameterType;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $name = $request->query->get('name');
        $dateParam = $request->query->get('date');
        $heure = $request->query->get('heure');

        $date = $dateParam ? new \DateTime($dateParam) : null;

        // 🔥 Only events of current user
        $events = $eventRepository->searchByCriteriaForUser(
            $user,
            $name,
            $date,
            $heure
        );

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }




    #[Route('/event/new', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();

        $form = $this->createForm(EventTypeForm::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setUser($this->getUser());

            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/event/{id}/edit', name: 'app_event_edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EventTypeForm::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_event');
        }

        return $this->render('event/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }

    #[Route('/event/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_event_' . $event->getId(), $request->request->get('_token'))) {
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('app_event');
    }

    #[Route('/event-admin/{id}/delete', name: 'app_event_delete_admin', methods: ['POST'])]
    public function deleteAdmin(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_event_' . $event->getId(), $request->request->get('_token'))) {
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_event_index');
    }

    #[Route('/event/filter', name: 'app_event_filter', methods: ['GET'])]
    public function filter(Request $request, EventRepository $eventRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $name = $request->query->get('name');
        $dateParam = $request->query->get('date');
        $heure = $request->query->get('heure');

        $date = null;
        if ($dateParam) {
            try {
                $date = new \DateTime($dateParam);
            } catch (\Exception $e) {
                $date = null;
            }
        }

        $events = $eventRepository->searchByCriteriaForUser(
            $user,
            $name,
            $date,
            $heure
        );

        return $this->render('event/_table.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/admin/events', name: 'app_admin_event_index')]
    public function adminIndex(EventRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $events = $repository->findAll();

        return $this->render('admin/event/index.html.twig', [
            'events' => $events,
        ]);
    }
    #[Route('/admin/event/{id}/edit', name: 'app_admin_event_edit', methods: ['POST'])]
    public function adminEdit(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $event->setName($request->request->get('name'));
        $event->setDate(new \DateTime($request->request->get('date')));
        $event->setHeure($request->request->get('heure'));
        $event->setAddresse($request->request->get('addresse'));
        $event->setDescription($request->request->get('description'));
        $latitude = $request->request->get('latitude');
        $longitude = $request->request->get('longitude');
        $event->setLatitude($latitude !== '' ? (float) $latitude : null);
        $event->setLongitude($longitude !== '' ? (float) $longitude : null);
        $em->flush();

        return $this->redirectToRoute('app_admin_event_index');
    }

    #[Route('/event/{id}/register', name: 'app_event_register')]
    public function register(
        Event $event,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$event->getParticipants()->contains($user)) {

            // ✅ Add participant
            $event->addParticipant($user);
            $em->flush();

            // ==========================
            // 🎟 CREATE QR CONTENT (JSON)
            // ==========================
            $qrContent = json_encode([
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                'event_date' => $event->getDate()?->format('d/m/Y'),
                'event_time' => $event->getHeure(),
                'user_id' => $user->getId(),
                'user_email' => $user->getEmail(),
                'user_name' => $user->getFullName(),
            ]);

            // ==========================
            // 🔲 GENERATE QR CODE
            // ==========================
            $qrCode = new QrCode(
                data: $qrContent,
                size: 300,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrImage = $result->getString();

            // ==========================
            // ✉ CREATE EMAIL
            // ==========================
            $email = (new Email())
                ->from('walahamdi0@gmail.com')
                ->to($user->getEmail())
                ->subject('Votre billet - QR Code')
                ->html("
                <h2>Bonjour {$user->getFullName()}</h2>
                <p>Votre inscription à l'événement est confirmée :</p>
                <strong>{$event->getName()}</strong><br>
                📅 {$event->getDate()?->format('d/m/Y')}<br>
                ⏰ {$event->getHeure()}<br>
                📍 {$event->getAddresse()}<br><br>
                <p>Présentez ce QR Code à l'entrée :</p>
                <img src='cid:qrcode'><br><br>
                Merci pour votre participation.
            ")
                ->embed($qrImage, 'qrcode', 'image/png');

            $mailer->send($email);
        }

        return $this->redirectToRoute('app_event_public');
    }

    #[Route('/events/public', name: 'app_event_public')]
    public function publicEvents(EventRepository $repository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $events = $repository->findAll();

        return $this->render('event/public.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/{id}/unregister', name: 'app_event_unregister')]
    public function unregister(Event $event, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if ($event->getParticipants()->contains($user)) {
            $event->removeParticipant($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_event_public');
    }


    #[Route('/events/suggested', name: 'app_event_suggested')]
    public function suggested(
        HttpClientInterface $client,
        EventRepository $eventRepository
    ): Response {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        // Get all events
        $events = $eventRepository->findAll();

        if (empty($events)) {
            return $this->render('event/suggested.html.twig', [
                'recommendedEvents' => [],
                'events' => []
            ]);
        }

        // Prepare events for AI
        $eventData = [];

        foreach ($events as $event) {
            $eventData[] = [
                'id' => $event->getId(),
                'description' => $event->getDescription()
            ];
        }

        // Get user pet types
        $userPetTypes = [];

        foreach ($user->getPets() as $pet) {
            $type = $pet->getTypePet();

            if ($type !== null && isset($type->name)) {
                $userPetTypes[] = strtolower($type->name);
            }
        }

        $userPetTypes = array_filter($userPetTypes);

        $recommendedIds = [];

        if (!empty($userPetTypes)) {

            try {

                $response = $client->request(
                    'POST',
                    'http://127.0.0.1:8001/recommend',
                    [
                        'json' => [
                            'user_pet_types' => $userPetTypes,
                            'events' => $eventData
                        ]
                    ]
                );

                $data = $response->toArray();
                $recommendedIds = $data['recommended_event_ids'] ?? [];
                // dump($recommendedIds);
                // die();

            } catch (\Exception $e) {
                // AI failed → no recommended events
                $recommendedIds = [];
            }
        }


        $recommendedEvents = [];

        if (!empty($recommendedIds)) {
            $recommendedEvents = $eventRepository->findBy([
                'id' => $recommendedIds
            ]);
        }

        // dump($recommendedEvents);
        // die();

        return $this->render('event/suggested.html.twig', [
            'recommendedEvents' => $recommendedEvents,
            'events' => $events
        ]);
    }
}
