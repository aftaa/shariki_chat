<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MobileController extends AbstractController
{
    #[Route('/mobile', name: 'app_mobile')]
    public function index(Request $request): Response
    {
        return $this->render('mobile/new.html.twig', [
            'local' => 'localhost' == $request->server->get('SERVER_NAME'),
        ]);
    }

    #[Route('/mobile/all', name: 'app_mobile_all')]
    public function all(Request $request): Response
    {
        return $this->render('mobile/all.html.twig', [
            'local' => 'localhost' == $request->server->get('SERVER_NAME'),
        ]);
    }
}
