<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function chat(Request $request): Response
    {
        return $this->render('chat/index.html.twig', [
            'local' => 'localhost' == $request->server->get('SERVER_NAME'),
        ]);
    }

    #[Route('/archive', name: 'app_archive')]
    public function archive(Request $request): Response
    {
        return $this->render('chat/archive.html.twig', [
            'local' => 'localhost' == $request->server->get('SERVER_NAME'),
        ]);
    }
}
