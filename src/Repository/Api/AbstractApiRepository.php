<?php

declare(strict_types=1);

namespace App\Repository\Api;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\{ClientExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiRepository
{
    private HttpClientInterface $client;
    protected SerializerInterface $serializer;

    public function __construct(HttpClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @param string      $url
     * @param string      $type
     * @param string      $method
     * @param string|null $body
     * @param string|null $token
     * @return array|object
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function fetchEndpoint(
        string $url,
        string $type,
        string $method = 'GET',
        string $body = null,
        string $token = null,
        $json = null
    ) {
        $headers = ($token !== null) ? ["Authorization: Bearer {$token}"] : null;
        $response = $this->client->request($method, $url, ['headers' => $headers, 'body' => $body, 'json' => $json]);

        return $this->handleSuccess($response, $type);
    }

    /**
     * @param ResponseInterface $response
     * @param                   $type
     * @return array|object
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function handleSuccess(ResponseInterface $response, $type)
    {
        return $this->serializer->deserialize($response->getContent(), $type, 'json');
    }
}
