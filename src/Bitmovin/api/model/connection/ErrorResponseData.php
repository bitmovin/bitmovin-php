<?php

namespace Bitmovin\api\model\connection;

class ErrorResponseData extends ResponseData
{
    /** @var  int required */
    private $code;

    /** @var  string required */
    private $message;

    /** @var  string required */
    private $developerMessage;

    /** @var  Link[] */
    private $links = array();

    /** @var  Message[] */
    private $details = array();

    /**
     * ResponseErrorData constructor.
     *
     * @param int    $code
     * @param string $message
     * @param string $developerMessage
     */
    public function __construct($code, $message, $developerMessage)
    {
        $this->code = $code;
        $this->message = $message;
        $this->developerMessage = $developerMessage;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return ErrorResponseData
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return ErrorResponseData
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getDeveloperMessage()
    {
        return $this->developerMessage;
    }

    /**
     * @param string $developerMessage
     *
     * @return ErrorResponseData
     */
    public function setDeveloperMessage($developerMessage)
    {
        $this->developerMessage = $developerMessage;
    }

    /**
     * @return Link[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param Link[] $links
     *
     * @return ErrorResponseData
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * @return Message[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param Message[] $details
     *
     * @return ErrorResponseData
     */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }

}