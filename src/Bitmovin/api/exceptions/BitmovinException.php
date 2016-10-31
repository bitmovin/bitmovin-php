<?php

namespace Bitmovin\api\exceptions;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class BitmovinException extends \Exception
{
    private $apiResponse = NULL;
    private $developerMessage = NULL;

    public function __construct($message = "", $code = 0, Exception $previous = NULL, $developerMessage = NULL)
    {
        parent::__construct($message, $code, $previous);

        $this->developerMessage = $developerMessage;
        $response = NULL;

        if ($previous instanceof RequestException)
        {
            $response = $previous->getResponse();
        }

        if (!is_null($response))
        {
            $this->apiResponse = $response;
        }
    }

    /**
     * @return null|string
     */
    public function getDeveloperMessage()
    {
        return $this->developerMessage;
    }

    /**
     * @return null|ResponseInterface
     */
    public function getApiResponse()
    {
        return $this->apiResponse;
    }
}