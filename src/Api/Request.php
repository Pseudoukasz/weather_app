<?php

namespace App\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class Request
{
    private string $baseUrl;
    private array $headers;

    public function __construct(string $baseUrl, array $headers = ['Accept' => 'application/json'])
    {
        $this->baseUrl = $baseUrl;
        $this->headers = $headers;
    }

    /**
     * @throws GuzzleException
     */
    public function request(string $endpoint, $params = null): ResponseInterface
    {
        return (new Client())->get($this->baseUrl . $endpoint . $params,  $this->headers);
    }
}
