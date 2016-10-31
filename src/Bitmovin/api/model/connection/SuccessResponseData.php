<?php

namespace Bitmovin\api\model\connection;

class SuccessResponseData extends ResponseData
{
    /** @var IResult */
    private $result;

    /** @var Message[] */
    private $messages = array();

    /**
     * SuccessResponseData constructor.
     *
     * @param IResult $result
     * @param array   $messages
     */
    public function __construct(IResult $result, array $messages)
    {
        $this->result = $result;
        $this->messages = $messages;
    }

    /**
     * @param $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return IResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}