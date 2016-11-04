<?php

namespace Bitmovin\api\model;

use Bitmovin\api\model\connection\ResponseEnvelope;
use JMS\Serializer\Annotation as JMS;

abstract class AbstractModel implements ModelInterface
{
    /**
     * @JMS\Type("string")
     * @var string UUID
     */
    protected $id;

    /** @var  ResponseEnvelope */
    protected $responseEnvelope;

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
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    public function getResponseEnvelope()
    {
        return $this->responseEnvelope;
    }

    /**
     * @param ResponseEnvelope $responseEnvelope
     *
     */
    public function setResponseEnvelope(ResponseEnvelope $responseEnvelope)
    {
        $this->responseEnvelope = $responseEnvelope;
    }
}