<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class AC3AudioCodecConfiguration extends AudioConfiguration
{
    /**
     * @JMS\Type("string")
     * @var string AC3ChannelLayout
     */
    private $channelLayout;

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

}
