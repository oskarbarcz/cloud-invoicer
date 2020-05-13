<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function dd;

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
     * @Route("/login", name="sso_base")
     * @return Response
     */
    public function login()
    {
        return $this->render('pages/login.html.twig');
    }

    /**
     * @Route("/sso-request", name="sso_request")
     * @param Request $request
     * @return Response
     */
    public function redirectToSso(Request $request): Response
    {
        if ($this->getUser() instanceof Account) {
            return $this->redirectToRoute('complete');
        }

        $redirect = "furtherRedirect=http://{$this->selfHost}/sso-success";
        $url = "http://".$this->ssoHost."/sso/login?".$redirect."&localSessionId=".$request->getClientIp();

        return $this->redirect($url);
    }

    /**
     * Catch SSO success message
     *
     * @Route("/sso-success", name="sso_receive")
     *
     * @param Request          $request
     * @param SessionInterface $session
     * @return Response
     */
    public function receive(Request $request, SessionInterface $session): Response
    {
        $client = HttpClient::create();
        $token = $request->get('token');

        try {
            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                ],
            ];

            $response = $client->request('GET', 'http://'.$this->ssoHost.'/api/account/current', $options);
            $content = $response->getContent();
        } catch (TransportExceptionInterface $e) {
            dd($e);
        } catch (ClientExceptionInterface $e) {
            dd($e);
        } catch (RedirectionExceptionInterface $e) {
            dd($e);
        } catch (ServerExceptionInterface $e) {
            dd($e);
        }
        dd($content);
        $account = $this->serializer->deserialize($content, Account::class, 'json');


        $session->set('user', $account);


        return $this->redirectToRoute('complete');
    }

    /**
     * @Route("/complete", name="complete")
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
