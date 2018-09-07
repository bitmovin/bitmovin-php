<?php

namespace Bitmovin\api;

use Bitmovin\api\enum\HttpMethod;
use Bitmovin\api\exceptions\BitmovinException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;

abstract class AbstractHttpClient
{
    /** @var Client */
    private static $httpClient = NULL;

    private $requiredOptions = array(
        "base_uri" => "https://api.bitmovin.com",
        "verify" => false,
        "http_errors" => true,
        "connect_timeout" => 60,
        "timeout" => 0,
        "debug" => false,
        "allow_redirects" => true
    );

    /**
     * BaseApiClient constructor.
     *
     * @param        $apiKey
     * @param string $version
     * @param string $endpointUrl
     */
    public function __construct($apiKey, $version = "v1", $endpointUrl = "https://api.bitmovin.com")
    {
        $this->initHttpClient($apiKey, $version, $endpointUrl);
    }

    /**
     * @param $apiKey
     * @param $version
     * @param $endpointUrl
     *
     * @return Client
     */
    private function initHttpClient($apiKey, $version, $endpointUrl)
    {
        $usedEndpointUrl = $this->requiredOptions["base_uri"];
        if (filter_var($endpointUrl, FILTER_VALIDATE_URL))
        {
            $usedEndpointUrl = $endpointUrl;
        }

        $this->requiredOptions["base_uri"] = trim($usedEndpointUrl, "/") . "/" . $version . "/";
        $this->requiredOptions['headers'] = array(
            'X-Api-Key' => $apiKey,
            'Content-Type' => 'application/json',
            'X-Api-Client' => 'bitmovin-php',
            'X-Api-Client-Version' => '1.5.19'
        );
        self::$httpClient = new Client($this->getRequiredOptions());

        return self::$httpClient;
    }

    /**
     * @param     $uri
     *
     * @param int $offset
     * @param int $limit
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws BitmovinException
     */
    protected function listRequest($uri, $offset = 0, $limit = 25)
    {
        $uri .= "?offset=$offset&limit=$limit";
        return $this->getRequest($uri);
    }

    /**
     * @param $uri
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws BitmovinException
     */
    protected function getRequest($uri)
    {
        return $this->sendRequest(HttpMethod::GET, ltrim($uri, "/"));
    }

    /**
     * @param $uri
     * @param $content
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws BitmovinException
     */
    protected function postRequest($uri, $content)
    {
        return $this->sendRequest(HttpMethod::POST, ltrim($uri, "/"), array(
            'body' => $content
        ));
    }

    /**
     * @param $uri
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws BitmovinException
     */
    protected function deleteRequest($uri)
    {
        return $this->sendRequest(HttpMethod::DELETE, ltrim($uri, "/"));
    }

    /**
     * @param        $method
     * @param string $uri
     * @param array  $options
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws BitmovinException
     */
    private function sendRequest($method, $uri = '', $options = array())
    {
        try
        {
            return static::getHttpClient()->request($method, $uri, $options);
        }
        catch (ConnectException $e)
        {
            throw new BitmovinException($e->getMessage(), $e->getCode(), $e);
        }
        catch (ClientException $e)
        {
            throw new BitmovinException($e->getMessage(), $e->getCode(), $e);
        }
        catch (ServerException $e)
        {
            throw new BitmovinException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return Client
     */
    private static function getHttpClient()
    {
        return self::$httpClient;
    }

    /**
     * @return array
     */
    private function getRequiredOptions()
    {
        return $this->requiredOptions;
    }
}
