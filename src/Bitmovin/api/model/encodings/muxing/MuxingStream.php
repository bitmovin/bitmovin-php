<?php


namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class MuxingStream
{

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $streamId;

    /**
     * @return string
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @param string $streamId
     */
    public function setStreamId($streamId)
    {
        $this->streamId = $streamId;
    }


}