<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 14.03.18
 * Time: 10:57
 */

namespace Bitmovin\api\model\encodings\streams\inputAnalysis;

use JMS\Serializer\Annotation as JMS;

class StreamInputAnalysisVideoStream
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $id;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $position;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $duration;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $codec;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $fps;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $bitrate;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $width;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $height;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param string $codec
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;
    }

    /**
     * @return string
     */
    public function getFps()
    {
        return $this->fps;
    }

    /**
     * @param string $fps
     */
    public function setFps($fps)
    {
        $this->fps = $fps;
    }

    /**
     * @return int
     */
    public function getBitrate()
    {
        return $this->bitrate;
    }

    /**
     * @param int $bitrate
     */
    public function setBitrate($bitrate)
    {
        $this->bitrate = $bitrate;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}