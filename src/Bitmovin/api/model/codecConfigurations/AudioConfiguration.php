<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class AudioConfiguration extends CodecConfiguration
{

    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $rate;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $bitrate;

    /**
     * Constructor.
     *
     * @param string $name
     * @param int    $bitrate Target audio bitrate in bps
     * @param double $rate    Target audio sample rate in Hz
     */
    protected function __construct($name, $bitrate, $rate)
    {
        parent::__construct($name);
        $this->rate = $rate;
        $this->bitrate = $bitrate;
    }


    /**
     * @return double
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param double $rate
     */
    public function setRate($rate)
    {
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


}