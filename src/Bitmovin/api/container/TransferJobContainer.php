<?php


namespace Bitmovin\api\container;


use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\TransferConfig;
use Bitmovin\output\BitmovinGcpOutput;
use Bitmovin\output\BitmovinAwsOutput;
use Bitmovin\output\FtpOutput;
use Bitmovin\output\GcsOutput;
use Bitmovin\output\S3Output;

class TransferJobContainer
{

    /**
     * @var TransferConfig
     */
    public $transferConfig;

    /**
     * @var TransferContainer[]
     */
    public $transferContainers = array();

    /**
     * @var Output
     */
    public $apiOutput;

    public function getOutputPath()
    {
        $output = $this->transferConfig->output;
        if ($output instanceof GcsOutput || $output instanceof S3Output || $output instanceof BitmovinGcpOutput
            || $output instanceof BitmovinAwsOutput)
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
}