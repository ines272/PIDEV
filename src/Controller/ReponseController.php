<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/admin/reponse')]
final class ReponseController extends AbstractController
{
    #[Route('/reclamation/{id}/add', name: 'app_reponse_add', methods: ['POST'])]
public function add(
    Request $request,
    Reclamation $reclamation,
    EntityManagerInterface $em,
    MailerInterface $mailer
): Response {
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $reponse = new Reponse();
    $reponse->setReclamation($reclamation);
    $reponse->setContenu($request->request->get('contenu'));
    $reponse->setAuteur($request->request->get('auteur'));

    if ($reclamation->getStatut() === 'en_attente') {
        $reclamation->setStatut('en_cours');
    }

    $em->persist($reponse);
    $em->flush();

    // EMAIL
    try {
        $email = (new Email())
            ->from('noreply@sitmypet.com')
            ->to($reclamation->getEmailClient())
            ->subject('Nouvelle réponse à votre réclamation #' . $reclamation->getId())
            ->html('<p>Votre réclamation a reçu une réponse.</p><p>' . $reponse->getContenu() . '</p>');

        $mailer->send($email);
    } catch (\Exception $e) {}

    return $this->redirectToRoute('app_admin_reclamation_index');
}


    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Réponse modifiée avec succès.');
            return $this->redirectToRoute('app_reclamation_show', ['id' => $reponse->getReclamation()->getId()]);
        }

        return $this->render('reponse_back/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $em): Response
    {
        $reclamationId = $reponse->getReclamation()->getId();

        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($reponse);
            $em->flush();

            $this->addFlash('success', 'Réponse supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reclamation_show', ['id' => $reclamationId]);
    }
}