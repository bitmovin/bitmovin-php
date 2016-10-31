<?php


namespace Bitmovin\api\model\manifests\dash;


use Bitmovin\api\model\AbstractModel;

use JMS\Serializer\Annotation as JMS;

class ContentProtection extends AbstractModel
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
    private $streamId;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $muxingId;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $drmId;

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
    public function getDrmId()
    {
        return $this->drmId;
    }

    /**
     * @param string $drmId
     */
    public function setDrmId($drmId)
    {
        $this->drmId = $drmId;
    }


}