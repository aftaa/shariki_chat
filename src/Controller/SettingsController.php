<?php

namespace App\Controller;

use App\Manager\ChatManager;
use App\Manager\OperatorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    #[Route('/settings', name: 'app_settings')]
    public function index(): Response
    {
        return $this->render('settings/index.html.twig', [
            'local' =>'localhost' === $_SERVER['SERVER_NAME'],
        ]);
    }
}
