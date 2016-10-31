<?php


namespace Bitmovin\configs;


use Bitmovin\configs\manifest\AbstractOutputFormat;
use Bitmovin\output\AbstractOutput;

class JobConfig
{

    /**
     * @var EncodingProfileConfig
     */
    public $encodingProfile;

    /**
     * @var AbstractOutput
     */
    public $output;

    /**
     * @var AbstractOutputFormat[]
     */
    public $outputFormat = array();

}