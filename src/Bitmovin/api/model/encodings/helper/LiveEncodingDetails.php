<?php

namespace Bitmovin\api\model\encodings\helper;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class LiveEncodingDetails extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $streamKey;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $encoderIp;

    /**
     * @return string
     */
    public function getStreamKey()
    {
        return $this->streamKey;
    }

    /**
     * @param string $streamKey
     */
    public function setStreamKey($streamKey)
    {
        $this->streamKey = $streamKey;
    }

    /**
     * @return string
     */
    public function getEncoderIp()
    {
        return $this->encoderIp;
    }

    /**
     * @param string $encoderIp
     */
    public function setEncoderIp($encoderIp)
    {
        $this->encoderIp = $encoderIp;
    }
}