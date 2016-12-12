<?php


namespace Bitmovin\configs\video;


use Bitmovin\api\enum\codecConfigurations\H265Profile;
use Bitmovin\configs\images\ThumbnailConfig;


class H265VideoStreamConfig extends AbstractVideoStreamConfig
{
    /**
     * @var string Enum: \Bitmovin\api\enum\codecConfigurations\H264Profile
     */
    public $profile = H265Profile::MAIN;
}