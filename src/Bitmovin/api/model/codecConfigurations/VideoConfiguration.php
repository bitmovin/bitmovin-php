<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

abstract class VideoConfiguration extends CodecConfiguration
{

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $bitrate;
    /**
     * @JMS\Type("float")
     * @var  float
     */
    private $rate;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $width;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $height;

    /**
     * Constructor.
     *
     * @param string $name
     * @param int    $bitrate Target bitrate for the encoded video in bps (bits per second)
     * @param float  $rate    Target frame rate of the encoded video
     */
    protected function __construct($name, $bitrate, $rate)
    {
        parent::__construct($name);
        $this->bitrate = $bitrate;
        $this->rate = $rate;
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
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
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