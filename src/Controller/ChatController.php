<?php

namespace App\Controller;

use App\Service\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(
        private ServerService $serverService,
    )
    {
    }

    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request): Response
    {
        return $this->render('chat/index.html.twig', [
            'server' => $this->serverService->get(),
        ]);
    }

    #[Route('/archive', name: 'app_archive')]
    public function archive(Request $request): Response
    {
        return $this->render('chat/archive.html.twig', [
            'server' => $this->serverService->get(),
        ]);
    }
}
