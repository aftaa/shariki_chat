<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkModeController extends AbstractController
{
    #[Route('/work/mode', name: 'app_work_mode')]
    public function index(): Response
    {
        return $this->render('work_mode/index.html.twig', [
            'controller_name' => 'WorkModeController',
        ]);
    }
}
