<?php

namespace Bitmovin\api\model\encodings\pertitle;

use JMS\Serializer\Annotation as JMS;

class PerTitle
{
    /**
     * @JMS\Type("Bitmovin\api\model\encodings\pertitle\H264PerTitleConfiguration")
     * @JMS\SerializedName("h264Configuration")
     * @var H264PerTitleConfiguration $h264PerTitleConfiguration
     */
    private $h264PerTitleConfiguration;

    /**
     * @return H264PerTitleConfiguration
     */
    public function getH264PerTitleConfiguration()
    {
        return $this->h264PerTitleConfiguration;
    }

    /**
     * @param H264PerTitleConfiguration $h264PerTitleConfiguration
     */
    public function setH264PerTitleConfiguration($h264PerTitleConfiguration)
    {
        $this->h264PerTitleConfiguration = $h264PerTitleConfiguration;
    }
}
