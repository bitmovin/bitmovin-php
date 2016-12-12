<?php


namespace Bitmovin\api\container;


use Bitmovin\api\enum\Status;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\JobConfig;
use Bitmovin\output\AbstractBitmovinOutput;
use Bitmovin\output\FtpOutput;
use Bitmovin\output\GcsOutput;
use Bitmovin\output\S3Output;

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
     * @var ManifestContainer[]
     */
    public $manifestContainers = array();

    /**
     * @var Output
     */
    public $apiOutput;

    public function deleteFinishedEncodings()
    {
        foreach ($this->encodingContainers as $encodingContainer)
        {
            if ($encodingContainer->status == Status::FINISHED)
            {
                $encodingContainer->deleteEncoding();
            }
        }
    }

    /**
     * @param $prefix
     * @return string
     */
    private function addTrailingSlash($prefix)
    {
        if (substr($prefix, -1) != '/')
        {
            $prefix .= '/';
        }
        return $prefix;
    }

    /**
     * @param $prefix
     * @return string
     */
    private function addLeadingSlash($prefix)
    {
        if (substr($prefix, 1) != '/')
        {
            $prefix = '/' . $prefix;
        }
        return $prefix;
    }

    /**
     * @param $prefix
     * @return string
     */
    private function stripSlashes($prefix)
    {
        if (substr($prefix, 0, 1) == '/')
        {
            $prefix = substr($prefix, 1);
        }
        if (substr($prefix, -1) == '/')
        {
            $prefix = substr($prefix, 0, -1);
        }
        return $prefix;
    }

    public function getOutputPath()
    {
        $output = $this->job->output;
        if ($output instanceof GcsOutput || $output instanceof S3Output || $output instanceof AbstractBitmovinOutput)
        {
            $prefix = $this->stripSlashes($output->prefix);
            return $this->addTrailingSlash($prefix);
        }
        else if ($output instanceof FtpOutput)
        {
            $prefix = $this->stripSlashes($output->prefix);
            $prefix = $this->addLeadingSlash($prefix);
            return $this->addTrailingSlash($prefix);
        }
        throw new \InvalidArgumentException();
    }

}