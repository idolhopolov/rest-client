<?php

namespace Merce\RestClient\HttpPlug\src\Service\Client\Curl\Builder\CurlBuilder;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Merce\RestClient\HttpPlug\src\DTO\Curl\Request\ICurlRequestPack;
use Merce\RestClient\HttpPlug\src\Service\Client\Curl\Builder\CurlExecutor\Impl\CurlClientContextExecutor;

interface ICurlBuilder
{
    public function setCURLOPTCUSTOMREQUEST(string $CURLOPT_CUSTOMREQUEST): self;
    public function setCURLOPTURL(string $CURLOPT_URL): self;
    public function setCURLOPTHTTPHEADER(array $CURLOPT_HTTPHEADER): self;
    public function setCURLOPTHTTPVERSION(string $CURLOPT_HTTP_VERSION): self;
    public function setCURLOPTUSERPWD(string $CURLOPT_USERPWD): self;
    public function setHttpMethod(string $httpMethod, ?Stream $body = null): self;
    public function setCURLOPTSSLVERIFYPEER(bool $CURLOPT_SSL_VERIFYPEER = false): self;
    public function buildPSRRequest(): RequestInterface;
    public function buildRequest(): ICurlRequestPack;
    public function buildExecutionContext(): CurlClientContextExecutor;
}