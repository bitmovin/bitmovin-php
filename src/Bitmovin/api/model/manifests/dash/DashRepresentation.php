<?php


namespace Bitmovin\api\model\manifests\dash;


use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class DashRepresentation extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string DashMuxingType enum
     */
    private $type;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $muxingId;
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
    private $segmentPath;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $startSegmentNumber;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $endSegmentNumber;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getSegmentPath()
    {
        return $this->segmentPath;
    }

    /**
     * @param string $segmentPath
     */
    public function setSegmentPath($segmentPath)
    {
        $this->segmentPath = $segmentPath;
    }

    /**
     * @return integer
     */
    public function getStartSegmentNumber()
    {
        return $this->startSegmentNumber;
    }

    /**
     * @param integer $startSegmentNumber
     */
    public function setStartSegmentNumber($startSegmentNumber)
    {
        $this->startSegmentNumber = $startSegmentNumber;
    }

    /**
     * @return integer
     */
    public function getEndSegmentNumber()
    {
        return $this->endSegmentNumber;
    }

    /**
     * @param integer $endSegmentNumber
     */
    public function setEndSegmentNumber($endSegmentNumber)
    {
        $this->endSegmentNumber = $endSegmentNumber;
    }
}