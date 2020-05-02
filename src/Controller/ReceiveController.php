<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceiveController extends AbstractController
{
    /**
     * Catch SSO success message
     *
     * @Route("/sso-success/{$token}")
     * @param string $token
     * @return Response
     */
    public function receive(string $token): Response
    {
        return $this->render('received.html.twig', ['token' => $token]);
    }
}
