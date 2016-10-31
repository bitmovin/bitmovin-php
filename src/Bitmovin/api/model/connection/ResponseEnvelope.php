<?php

namespace Bitmovin\api\model\connection;

class ResponseEnvelope implements IResponse
{
    /** @var  string UUID */
    private $id;
    /** @var  string UUID */
    private $requestId;
    /** @var  string(SUCCESS|ERROR) ResponseStatus */
    private $status;
    /** @var  ResponseData */
    private $data;
    /** @var  \stdClass */
    private $more;

    public function __construct($requestId, $status, $data = NULL, \stdClass $more = NULL)
    {
        $this->setRequestId($requestId);
        $this->setStatus($status);
        $this->setData($data);
        $this->setMore($more);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return ResponseEnvelope
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $requestId
     *
     * @return ResponseEnvelope
     */
    private function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @param string $status
     *
     * @return ResponseEnvelope
     */
    private function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param ResponseData $data
     *
     * @return ResponseEnvelope
     */
    private function setData($data = NULL)
    {
        $this->data = $data;
    }

    /**
     * @param \stdClass $more
     *
     * @return ResponseEnvelope
     */
    private function setMore(\stdClass $more = NULL)
    {
        $this->more = $more;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return ResponseData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return \stdClass
     */
    public function getMore()
    {
        return $this->more;
    }
}