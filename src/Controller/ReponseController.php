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
            ->from('your.actual.email@gmail.com')   // ← must match your Gmail
            ->to($reclamation->getEmailClient())
            ->subject('Nouvelle réponse à votre réclamation #' . $reclamation->getId())
            ->html('
                <div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;">
                    <h2 style="color:#6f42c1;">SitMyPet — Réponse à votre réclamation</h2>
                    <p>Bonjour <strong>' . $reclamation->getNomClient() . '</strong>,</p>
                    <p>Votre réclamation <strong>' . $reclamation->getSujet() . '</strong> a reçu une réponse :</p>
                    <div style="background:#f8f4ff;border-left:4px solid #6f42c1;padding:15px;border-radius:4px;">
                        ' . $reponse->getContenu() . '
                    </div>
                    <p style="color:#888;margin-top:20px;">L\'équipe SitMyPet</p>
                </div>
            ');

        $mailer->send($email);
        $this->addFlash('success', 'Réponse enregistrée et client notifié par email.');

    } catch (\Exception $e) {
        $this->addFlash('warning', 'Réponse enregistrée, mais email non envoyé : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_admin_reclamation_index');
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