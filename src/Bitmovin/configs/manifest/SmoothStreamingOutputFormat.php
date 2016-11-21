<?php


namespace Bitmovin\configs\manifest;


use Bitmovin\configs\drm\PlayReadyDrm;

class SmoothStreamingOutputFormat extends AbstractOutputFormat
{

    public $manifestName = 'manifest';
    public $clientManifestName = 'stream.ismc';
    public $serverManifestName = 'stream.ism';
    public $mediaFileName = 'stream.mp4';

    /**
     * @var PlayReadyDrm
     */
    public $playReady = null;

}