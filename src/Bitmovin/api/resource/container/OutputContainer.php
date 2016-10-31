<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\outputs\AzureOutput;
use Bitmovin\api\model\outputs\FtpOutput;
use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\model\outputs\SftpOutput;
use Bitmovin\api\resource\outputs\AzureOutputResource;
use Bitmovin\api\resource\outputs\FtpOutputResource;
use Bitmovin\api\resource\outputs\GcsOutputResource;
use Bitmovin\api\resource\outputs\S3OutputResource;
use Bitmovin\api\resource\outputs\SftpOutputResource;
use Bitmovin\api\util\ApiUrls;

class OutputContainer
{
    /** @var S3OutputResource */
    private $s3;
    /** @var  GcsOutputResource */
    private $gcs;
    /** @var  AzureOutputResource */
    private $azure;
    /** @var  FtpOutputResource */
    private $ftp;
    /** @var  SftpOutputResource */
    private $sftp;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->gcs = new GcsOutputResource(ApiUrls::OUTPUT_GCS, GcsOutput::class, $apiKey);
        $this->s3 = new S3OutputResource(ApiUrls::OUTPUT_S3, S3Output::class, $apiKey);
        $this->azure = new AzureOutputResource(ApiUrls::OUTPUT_AZURE, AzureOutput::class, $apiKey);
        $this->ftp = new FtpOutputResource(ApiUrls::OUTPUT_FTP, FtpOutput::class, $apiKey);
        $this->sftp = new SftpOutputResource(ApiUrls::OUTPUT_SFTP, SftpOutput::class, $apiKey);
    }


    /**
     * Creates an output
     * @param Output $output
     * @return Output
     */
    public function create(Output $output)
    {
        if ($output instanceof GcsOutput)
        {
            return $this->gcs()->create($output);
        }
        if ($output instanceof S3Output)
        {
            return $this->s3()->create($output);
        }
        if ($output instanceof AzureOutput)
        {
            return $this->azure()->create($output);
        }
        if ($output instanceof FtpOutput)
        {
            return $this->ftp()->create($output);
        }
        if ($output instanceof SftpOutput)
        {
            return $this->sftp()->create($output);
        }
        throw new \InvalidArgumentException();
    }

    /**
     * @return S3OutputResource
     */
    public function s3()
    {
        return $this->s3;
    }

    /**
     * @return GcsOutputResource
     */
    public function gcs()
    {
        return $this->gcs;
    }

    /**
     * @return AzureOutputResource
     */
    public function azure()
    {
        return $this->azure;
    }

    /**
     * @return FtpOutputResource
     */
    public function ftp()
    {
        return $this->ftp;
    }

    /**
     * @return SftpOutputResource
     */
    public function sftp()
    {
        return $this->sftp;
    }

}