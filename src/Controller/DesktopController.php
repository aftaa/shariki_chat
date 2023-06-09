<?php

namespace App\Controller;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesktopController extends AbstractController
{
    #[Route('/desktop', name: 'app_desktop')]
    public function index(): Response
    {
        return $this->render('desktop/index.html.twig', [
            'local' =>'localhost' === $_SERVER['SERVER_NAME'],
        ]);
    }
}
