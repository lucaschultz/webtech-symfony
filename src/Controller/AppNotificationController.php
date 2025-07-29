<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppNotificationController extends AbstractController
{
    #[Route('/app/notification', name: 'app_notification')]
    public function index(): Response
    {
        return $this->render('app_notification/index.html.twig', [
            'controller_name' => 'AppNotificationController',
        ]);
    }
}
