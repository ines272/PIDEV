<?php

namespace App\Controller;

use App\Repository\PetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Pet;
use App\Form\PetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class PetController extends AbstractController
{
    #[Route('/pet', name: 'app_pet')]
    public function index(Request $request, PetRepository $petRepository): Response
    {
        $name = $request->query->get('name');
        $type = $request->query->get('type');
        $gender = $request->query->get('gender');
        $vaccinated = $request->query->get('vaccinated');
        $critical = $request->query->get('critical');

        $typeEnum = $type ? \App\Enum\PetType::from($type) : null;
        $genderEnum = $gender ? \App\Enum\PetGender::from($gender) : null;

        $vaccinatedBool = $vaccinated !== null ? filter_var($vaccinated, FILTER_VALIDATE_BOOLEAN) : null;
        $criticalBool = $critical !== null ? filter_var($critical, FILTER_VALIDATE_BOOLEAN) : null;

        $pets = $petRepository->searchByCriteria(
            $name,
            $typeEnum,
            $genderEnum,
            $vaccinatedBool,
            $criticalBool
        );

        return $this->render('pet/index.html.twig', [
            'pets' => $pets,
        ]);
    }


    #[Route('/pet/new', name: 'app_pet_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $pet = new Pet();

        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pet);
            $em->flush();

            return $this->redirectToRoute('app_pet');
        }

        return $this->render('pet/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }


    #[Route('/pet/{id}/edit', name: 'app_pet_edit')]
    public function edit(Pet $pet, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PetType::class, $pet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // no persist needed, entity already managed
            return $this->redirectToRoute('app_pet');
        }

        return $this->render('pet/form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
        ]);
    }

    #[Route('/pet/{id}/delete', name: 'app_pet_delete', methods: ['POST'])]
    public function delete(Pet $pet, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_pet_' . $pet->getId(), $request->request->get('_token'))) {
            $em->remove($pet);
            $em->flush();
        }

        return $this->redirectToRoute('app_pet');
    }



    #[Route('/pet/filter', name: 'app_pet_filter', methods: ['GET'])]
    public function filter(Request $request, PetRepository $petRepository): Response
    {
        $name = $request->query->get('name');

        $pets = $petRepository->createQueryBuilder('p')
            ->andWhere('p.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();

        return $this->render('pet/_table.html.twig', [
            'pets' => $pets,
        ]);
    }

}
