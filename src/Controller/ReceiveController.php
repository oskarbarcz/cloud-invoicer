<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReceiveController extends AbstractController
{


    /**
     * @Route("/sso-base", name="sso_base")
     * @return Response
     */
    public function actionThatRedirectsToLogin()
    {
        return $this->render('redirect.html.twig');
    }

    /**
     * @Route("/sso-request", name="sso_request")
     */
    public function startRedirecting()
    {// action
    }

    /**
     * Catch SSO success message
     *
     * @Route("/sso-success/{token}", name="sso_receive")
     *
     * @param string $token
     * @return Response
     */
    public function receive(string $token): Response
    {
        return $this->render('received.html.twig', ['token' => $token]);
    }

    /**
     * @Route("/success")
     * @return Response
     */
    public function success()
    {
        return $this->render('success.html.twig');
    }
}
