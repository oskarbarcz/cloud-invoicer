<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private string $ssoHost;
    private string $selfHost;

    public function __construct(string $ssoHost, string $selfHost)
    {
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
            return $this->redirectToRoute('app_index');
        }

        $redirect = "furtherRedirect=http://{$this->selfHost}/login";
        $url = "http://".$this->ssoHost."/sso/login?".$redirect."&localSessionId=".$request->getClientIp();

        return $this->redirect($url);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
