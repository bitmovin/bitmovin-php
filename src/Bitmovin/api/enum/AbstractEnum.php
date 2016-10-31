<?php


namespace Bitmovin\api\enum;


use Bitmovin\api\model\connection\ResponseEnvelope;

abstract class AbstractEnum
{

    /** @var  ResponseEnvelope */
    protected $responseEnvelope;

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }

    public function getResponseEnvelope()
    {
        return $this->responseEnvelope;
    }

    /**
     * @param ResponseEnvelope $responseEnvelope
     *
     * @return self
     */
    public function setResponseEnvelope(ResponseEnvelope $responseEnvelope)
    {
        $this->responseEnvelope = $responseEnvelope;
    }

}