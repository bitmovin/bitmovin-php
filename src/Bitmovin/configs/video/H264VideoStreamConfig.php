<?php


namespace Bitmovin\configs\video;


use Bitmovin\api\enum\codecConfigurations\H264Profile;
use Bitmovin\configs\images\ThumbnailConfig;

class H264VideoStreamConfig extends AbstractVideoStreamConfig
{
    /**
     * @var string Enum: \Bitmovin\api\enum\codecConfigurations\H264Profile
     */
    public $profile = H264Profile::HIGH;

    /** @var ThumbnailConfig[] */
    public $thumbnailConfigs = array();
}