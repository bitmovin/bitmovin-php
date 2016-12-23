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

    public $fileName;
}