<?php


namespace Bitmovin\configs;


use Bitmovin\api\container\JobContainer;
use Bitmovin\output\AbstractOutput;

class TransferConfig
{
    /**
     * @var JobContainer
     */
    public $jobContainer;

    /**
     * @var AbstractOutput
     */
    public $output;

}