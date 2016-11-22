<?php


namespace Bitmovin\configs\video;


use Bitmovin\api\enum\codecConfigurations\H264Profile;

class H264VideoStreamConfig extends AbstractVideoStreamConfig
{
    private $id;

    /**
     * @var string Enum: \Bitmovin\api\enum\codecConfigurations\H264Profile
     */
    public $profile = H264Profile::HIGH;

    /**
     * H264VideoStreamConfig constructor.
     */
    public function __construct()
    {
        $this->id = uniqid("bitmovin_",true);
    }

    public function getId() {
        return $this->id;
    }
}