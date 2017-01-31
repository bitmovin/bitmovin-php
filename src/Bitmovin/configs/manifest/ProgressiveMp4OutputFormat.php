<?php

namespace Bitmovin\configs\manifest;

use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\drm\ClearKeyDrm;

class ProgressiveMp4OutputFormat extends AbstractOutputFormat
{
    /** @var  $streamConfigs AbstractStreamConfig[] */
    public $streamConfigs = array();

    /** @var ClearKeyDrm */
    public $clearKey = null;

    /**
     * @var string
     */
    public $folder = 'mp4/';

    /**
     * @var string
     */
    public $fileName;
}