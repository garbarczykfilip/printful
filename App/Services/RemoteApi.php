<?php

namespace App\Services;

use Psr\Http\Message\ResponseInterface;

class RemoteApi
{
    public const HTTP_OK = 200;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * @var mixed
     */
    private $client;

    /**
     * @param mixed $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function post(array $parameters): ResponseInterface
    {
        return $this->client->post(
            getenv('API_URI'),
            [
                'headers' => [
                    'Accept' => 'application/json',
                    "Authorization" => "Basic " . base64_encode(getenv('API_KEY')),
                    "Content-Type" => 'application/json; charset=UTF-8',
                ],
                "json" => $parameters,
            ]
        );
    }
}