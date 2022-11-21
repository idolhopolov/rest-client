<?php

declare(strict_types = 1);

namespace Merce\RestClient\HttpPlug\src;

use Psr\Http\Client\ClientInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Merce\RestClient\HttpPlug\src\Exception\Impl\LogicException;
use Merce\RestClient\HttpPlug\src\Exception\Impl\ClientException;
use Merce\RestClient\HttpPlug\src\MiddlewareContainer\IMiddlewareHandler;
use Merce\RestClient\HttpPlug\src\Exception\Impl\InvalidArgumentException;
use Merce\RestClient\HttpPlug\src\MiddlewareContainer\Impl\StackMiddlewareHandler;

class HttpPlugController
{

    private RequestInterface $lastRequest;

    private ResponseInterface $lastResponse;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory = new Psr17Factory(),
        private readonly IMiddlewareHandler $handler = new StackMiddlewareHandler()
    ) {
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @return ResponseInterface
     */
    public function get(string $url, array $headers = []): ResponseInterface
    {

        return $this->request('GET', $url, $headers);
    }

    /**
     * Sends a request.
     *
     * @param  string  $method  The request method to use
     * @param  string  $url  The URL to call
     * @param  array  $headers  An array of request headers
     * @param  string  $body  The request content
     *
     * @return ResponseInterface The response object
     */
    private function request(string $method, string $url, array $headers = [], string $body = ''): ResponseInterface
    {

        $request = $this->createRequest($method, $url, $headers, $body);

        return $this->sendRequest($request);
    }

    /**
     * @param  string  $method
     * @param  string  $url
     * @param  array  $headers
     * @param  string  $body
     * @return RequestInterface
     */
    private function createRequest(string $method, string $url, array $headers, string $body): RequestInterface
    {

        $request = $this->requestFactory->createRequest($method, $url);
        $request->getBody()->write($body);
        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }

    /**
     * @throws ClientException
     * @throws LogicException
     * @throws InvalidArgumentException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {

        $requestChainLast = function (RequestInterface $request, callable $responseChain) {

            $response = $this->client->sendRequest($request);
            $responseChain($request, $response);
        };
        $responseChainLast = function (RequestInterface $request, ResponseInterface $response) {

            $this->lastRequest = $request;
            $this->lastResponse = $response;
        };

        $callbackChain = $this->handler->resolve($requestChainLast, $responseChainLast);
        $callbackChain($request);

        return $this->lastResponse;
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @param  string  $body
     * @return ResponseInterface
     */
    public function post(string $url, array $headers = [], string $body = ''): ResponseInterface
    {

        return $this->request('POST', $url, $headers, $body);
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @return ResponseInterface
     */
    public function head(string $url, array $headers = []): ResponseInterface
    {

        return $this->request('HEAD', $url, $headers);
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @param  string  $body
     * @return ResponseInterface
     */
    public function patch(string $url, array $headers = [], string $body = ''): ResponseInterface
    {

        return $this->request('PATCH', $url, $headers, $body);
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @param  string  $body
     * @return ResponseInterface
     */
    public function put(string $url, array $headers = [], string $body = ''): ResponseInterface
    {

        return $this->request('PUT', $url, $headers, $body);
    }

    /**
     * @param  string  $url
     * @param  array  $headers
     * @param  string  $body
     * @return ResponseInterface
     */
    public function delete(string $url, array $headers = [], string $body = ''): ResponseInterface
    {

        return $this->request('DELETE', $url, $headers, $body);
    }
}