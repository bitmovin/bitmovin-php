<?php

namespace Bitmovin\api\model\manifests\smoothstreaming;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class SmoothStreamingRepresentation extends AbstractModel
{

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $encodingId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $muxingId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $mediaFile;

    /**
     * @return string
     */
    public function getEncodingId()
    {
        return $this->encodingId;
    }

    /**
     * @param string $encodingId
     */
    public function setEncodingId($encodingId)
    {
        $this->encodingId = $encodingId;
    }

    /**
     * @return string
     */
    public function getMuxingId()
    {
        return $this->muxingId;
    }

    /**
     * @param string $muxingId
     */
    public function setMuxingId($muxingId)
    {
        $this->muxingId = $muxingId;
    }

    /**
     * @return string
     */
    public function getMediaFile()
    {
        return $this->mediaFile;
    }

    /**
     * @param string $mediaFile
     */
    public function setMediaFile($mediaFile)
    {
        $this->mediaFile = $mediaFile;
    }

}