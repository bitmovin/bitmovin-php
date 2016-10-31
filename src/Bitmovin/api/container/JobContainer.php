<?php


namespace Bitmovin\api\container;


use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\JobConfig;
use Bitmovin\output\GcsOutput;

class JobContainer
{

    /**
     * @var JobConfig
     */
    public $job;

    /**
     * @var EncodingContainer[]
     */
    public $encodingContainers = array();

    /**
     * @var Output
     */
    public $apiOutput;

    public function getOutputPath()
    {
        $output = $this->job->output;
        if ($output instanceof GcsOutput)
        {
            return $output->prefix;
        }
        throw new \InvalidArgumentException();
    }


}