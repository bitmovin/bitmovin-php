<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class AACAudioCodecConfiguration extends AudioConfiguration
{
    /**
     * @JMS\Type("string")
     * @var string AACChannelLayout
     */
    private $channelLayout;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $volumeAdjust;
    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $normalize;

    /**
     * Constructor.
     *
     * @param string $name
     * @param int    $bitrate Target audio bitrate in bps
     * @param double $rate    Target audio sample rate in Hz
     */
    public function __construct($name, $bitrate, $rate)
    {
        parent::__construct($name, $bitrate, $rate);
    }

    /**
     * @return string
     */
    public function getChannelLayout()
    {
        return $this->channelLayout;
    }

    /**
     * @param string $channelLayout
     */
    public function setChannelLayout($channelLayout)
    {
        $this->channelLayout = $channelLayout;
    }

    /**
     * @return int
     */
    public function getVolumeAdjust()
    {
        return $this->volumeAdjust;
    }

    /**
     * @param int $volumeAdjust
     */
    public function setVolumeAdjust($volumeAdjust)
    {
        $this->volumeAdjust = $volumeAdjust;
    }

    /**
     * @return boolean
     */
    public function isNormalize()
    {
        return $this->normalize;
    }

    /**
     * @param boolean $normalize
     */
    public function setNormalize($normalize)
    {
        $this->normalize = $normalize;
    }


}