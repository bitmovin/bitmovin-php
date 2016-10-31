<?php


namespace Bitmovin\api\model\manifests\hls;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class StreamInfo extends AbstractModel
{

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $audio;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $video;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $subtitles;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $closedCaptions;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $encodingId;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $streamId;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $muxingId;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $drmId;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $segmentPath;

    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
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