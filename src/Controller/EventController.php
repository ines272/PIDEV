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

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
public function index(Request $request, EventRepository $eventRepository): Response
{
    $name = $request->query->get('name');
    $dateParam = $request->query->get('date');
    $heure = $request->query->get('heure');

    $date = $dateParam ? new \DateTime($dateParam) : null;

    $events = $eventRepository->searchByCriteria($name, $date, $heure);

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

   #[Route('/event/filter', name: 'app_event_filter', methods: ['GET'])]
public function filter(Request $request, EventRepository $eventRepository): Response
{
    $name = $request->query->get('name');
    $dateParam = $request->query->get('date');
    $heure = $request->query->get('heure');

    $date = $dateParam ? new \DateTime($dateParam) : null;

    $events = $eventRepository->searchByCriteria($name, $date, $heure);

    return $this->render('event/_table.html.twig', [
        'events' => $events,
    ]);
}


}
