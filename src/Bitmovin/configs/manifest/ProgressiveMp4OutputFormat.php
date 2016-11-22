<?php

namespace Bitmovin\configs\manifest;

use Bitmovin\configs\AbstractStreamConfig;

class ProgressiveMp4OutputFormat extends AbstractOutputFormat
{
    /** @var  $streamConfigs AbstractStreamConfig[] */
    public $streamConfigs = array();

    public $fileName;
}