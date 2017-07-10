<?php


namespace Bitmovin\api\model\manifests\hls;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class StreamInfo extends AbstractModel
{

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $audio;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $video;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $subtitles;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $closedCaptions;

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
     * @JMS\Type("string")
     * @var  string
     */
    private $uri;


    /**
     * @return string
     */
    public function getAudio()
    {
        return $this->audio;
    }

    /**
     * @param string $audio
     */
    public function setAudio($audio)
    {
        $this->audio = $audio;
    }

    /**
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param string $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return string
     */
    public function getSubtitles()
    {
        return $this->subtitles;
    }

    /**
     * @param string $subtitles
     */
    public function setSubtitles($subtitles)
    {
        $this->subtitles = $subtitles;
    }

    /**
     * @return string
     */
    public function getClosedCaptions()
    {
        return $this->closedCaptions;
    }

    /**
     * @param string $closedCaptions
     */
    public function setClosedCaptions($closedCaptions)
    {
        $this->closedCaptions = $closedCaptions;
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
     * @param integer $segmentPath
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
     * @param integer $segmentPath
     */
    public function setEndSegmentNumber($endSegmentNumber)
    {
        $this->endSegmentNumber = $endSegmentNumber;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

}