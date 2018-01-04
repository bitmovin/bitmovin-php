<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\outputs\AkamaiNetStorageOutput;
use Bitmovin\api\model\outputs\AzureOutput;
use Bitmovin\api\model\outputs\FtpOutput;
use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\model\outputs\GenericS3Output;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;
use Bitmovin\api\model\outputs\SftpOutput;
use Bitmovin\api\resource\outputs\AkamaiNetStorageOutputResource;
use Bitmovin\api\resource\outputs\AzureOutputResource;
use Bitmovin\api\resource\outputs\FtpOutputResource;
use Bitmovin\api\resource\outputs\GcsOutputResource;
use Bitmovin\api\resource\outputs\GenericS3OutputResource;
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
    /** @var GenericS3OutputResource */
    private $genericS3;
    /** @var AkamaiNetStorageOutputResource */
    private $akamaiNetStorage;

    /** @var  BitmovinOutputContainer */
    private $bitmovin;

    /**
     * OutputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->bitmovin = new BitmovinOutputContainer($apiKey);

        $this->gcs = new GcsOutputResource(ApiUrls::OUTPUT_GCS, GcsOutput::class, $apiKey);
        $this->s3 = new S3OutputResource(ApiUrls::OUTPUT_S3, S3Output::class, $apiKey);
        $this->azure = new AzureOutputResource(ApiUrls::OUTPUT_AZURE, AzureOutput::class, $apiKey);
        $this->ftp = new FtpOutputResource(ApiUrls::OUTPUT_FTP, FtpOutput::class, $apiKey);
        $this->sftp = new SftpOutputResource(ApiUrls::OUTPUT_SFTP, SftpOutput::class, $apiKey);
        $this->genericS3 = new GenericS3OutputResource(ApiUrls::OUTPUT_GENERIC_S3, GenericS3Output::class, $apiKey);
        $this->akamaiNetStorage = new AkamaiNetStorageOutputResource(ApiUrls::OUTPUT_AKAMAI_NET_STORAGE, AkamaiNetStorageOutput::class, $apiKey);
    }


    /**
     * Creates an output
     *
     * @param Output $output
     *
     * @return Output
     * @throws \Bitmovin\api\exceptions\BitmovinException
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
        if ($output instanceof GenericS3Output)
        {
            return $this->genericS3()->create($output);
        }
        if ($output instanceof AkamaiNetStorageOutput)
        {
            return $this->akamaiNetStorage()->create($output);
        }
        throw new \InvalidArgumentException();
    }

    /**
     * @return BitmovinOutputContainer
     */
    public function bitmovin()
    {
        return $this->bitmovin;
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

    /**
     * @return GenericS3OutputResource
     */
    public function genericS3()
    {
        return $this->genericS3;
    }

    /**
     * @return AkamaiNetStorageOutputResource
     */
    public function akamaiNetStorage()
    {
        return $this->akamaiNetStorage;
    }

}