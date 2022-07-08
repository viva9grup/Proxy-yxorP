<?php

namespace yxorP\parser\Storage;

use yxorP\parser\topInterfaceLevelDomainListInterface;
use yxorP\parser\topLevelDomains;
use yxorP\parser\unableToLoadResource;
use yxorP\psr\Http\Client\ClientExceptionInterface;
use yxorP\psr\Http\Client\ClientInterface;
use yxorP\psr\Http\Message\RequestFactoryInterface;

final class topLevelDomainListPsr18Client implements topLevelDomainListClientInterface
{
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory)
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    public function get(string $uri): topInterfaceLevelDomainListInterface
    {
        $request = $this->requestFactory->createRequest('GET', $uri);
        try {
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw unableToLoadResource::dueToUnavailableService($uri, $exception);
        }
        if (400 <= $response->getStatusCode()) {
            throw unableToLoadResource::dueToUnexpectedStatusCode($uri, $response->getStatusCode());
        }
        return topLevelDomains::fromString($response->getBody());
    }
}