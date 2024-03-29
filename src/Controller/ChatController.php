<?php

namespace App\Controller;

use App\Service\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    public function __construct(
        private readonly ServerService $serverService,
    )
    {
    }

    #[Route('/mobile')]
    public function new(): RedirectResponse
    {
        return $this->redirectToRoute('app_chat');
    }

    #[Route('/all')]
    public function all(): RedirectResponse
    {
        return $this->redirectToRoute('app_archive');
    }

    #[Route('/chat', name: 'app_chat')]
    public function chat(): Response
    {
        return $this->render('chat/index.html.twig', [
            'server' => $this->serverService->get(),
        ]);
    }

    #[Route('/archive', name: 'app_archive')]
    public function archive(): Response
    {
        return $this->render('chat/archive.html.twig', [
            'server' => $this->serverService->get(),
        ]);
    }

    #[Route('/recent', name: 'app_recent')]
    public function recent(): Response
    {
        return $this->render('chat/recent.html.twig', [
            'server' => $this->serverService->get(),
        ]);
    }
}
