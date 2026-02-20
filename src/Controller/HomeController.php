<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        if (!$this->getUser()) {
        return $this->redirectToRoute('app_landing');
        }   
        return $this->render('front/home/index.html.twig');
    }
}