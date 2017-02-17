<?php


namespace Bitmovin\api\container;


use Bitmovin\api\enum\Status;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\JobConfig;
use Bitmovin\helper\PathHelper;
use Bitmovin\output\AbstractBitmovinOutput;
use Bitmovin\output\FtpOutput;
use Bitmovin\output\GcsOutput;
use Bitmovin\output\S3Output;
use Bitmovin\output\GenericS3Output;

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

    /**
     * Get the encoding ids
     *
     * @return string[]
     */
    public function getEncodingIds()
    {
        $ids = array();
        foreach ($this->encodingContainers as $encodingContainer)
        {
            if ($encodingContainer->encoding != null)
                $ids[] = $encodingContainer->encoding->getId();
        }
        return $ids;
    }

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

    public function getOutputPath($postfix = '')
    {
        $output = $this->job->output;
        if ($output instanceof GcsOutput || $output instanceof S3Output || $output instanceof AbstractBitmovinOutput || $output instanceof GenericS3Output)
        {
            $path = $output->prefix;
            if ($postfix !== null && strlen($postfix) > 0)
                $path = PathHelper::combinePath($path, $postfix);
            $path = $this->stripSlashes($path);
            return $this->addTrailingSlash($path);
        }
        else if ($output instanceof FtpOutput)
        {
            $path = $output->prefix;
            if ($postfix !== null && strlen($postfix) > 0)
                $path = PathHelper::combinePath($path, $postfix);
            $path = $this->stripSlashes($path);
            $path = $this->addLeadingSlash($path);
            return $this->addTrailingSlash($path);
        }
        throw new \InvalidArgumentException();
    }

}