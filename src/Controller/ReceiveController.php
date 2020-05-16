<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ReceiveController extends AbstractController
{
    private SerializerInterface $serializer;
    private string $ssoHost;
    private string $selfHost;

    public function __construct(SerializerInterface $serializer, string $ssoHost, string $selfHost)
    {
        $this->serializer = $serializer;
        $this->ssoHost = $ssoHost;
        $this->selfHost = $selfHost;
    }


    /**
     * @Route("/login", name="app_sso-login")
     * @param Request $request
     * @return Response
     */
    public function redirectToSso(Request $request): Response
    {
        if ($this->getUser() instanceof Account) {
            return $this->redirectToRoute('app_login-success');
        }

        $redirect = "furtherRedirect=http://{$this->selfHost}/login";
        $url = "http://".$this->ssoHost."/sso/login?".$redirect."&localSessionId=".$request->getClientIp();

        return $this->redirect($url);
    }

    /**
     * @Route("/complete", name="app_login-success")
     * @param Request          $request
     * @param SessionInterface $session
     * @return Response
     */
    public function showAuthedUser(Request $request, SessionInterface $session): Response
    {
        return $this->render(
            'received.html.twig',
            [
                'token' => $request->get('token'),
                'user' => $session->get('user'),
            ]
        );
    }
}
