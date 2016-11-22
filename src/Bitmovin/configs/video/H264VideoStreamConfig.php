<?php


namespace Bitmovin\configs\video;


use Bitmovin\api\enum\codecConfigurations\H264Profile;

class H264VideoStreamConfig extends AbstractVideoStreamConfig
{
    /**
     * @var string Enum: \Bitmovin\api\enum\codecConfigurations\H264Profile
     */
    public $profile = H264Profile::HIGH;
}