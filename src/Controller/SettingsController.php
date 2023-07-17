<?php

namespace App\Controller;

use App\Service\ServerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(ServerService $serverService): Response
    {
        return $this->render('settings/index.html.twig', [
            'server' => $serverService->get(),
        ]);
    }
}
