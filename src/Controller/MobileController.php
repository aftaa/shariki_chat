<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MobileController extends AbstractController
{
    #[Route('/mobile', name: 'app_mobile')]
    public function index(): Response
    {
        return $this->render('mobile/new.html.twig', [
            'local' =>'localhost' === $_SERVER['SERVER_NAME'],
        ]);
    }

    #[Route('/mobile/all', name: 'app_mobile_all')]
    public function all(): Response
    {
        return $this->render('mobile/all.html.twig', [
            'local' =>'localhost' === $_SERVER['SERVER_NAME'],
        ]);
    }
}
