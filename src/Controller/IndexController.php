<?php

namespace App\Controller;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'local' =>'shariki-chat' === $_SERVER['SERVER_NAME'],
        ]);
    }

    #[Route('/setup', name: 'app_setup')]
    public function setup(): void
    {
    }
}
