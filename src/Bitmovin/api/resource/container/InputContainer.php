<?php

namespace Bitmovin\api\resource\container;

use Bitmovin\api\model\inputs\AsperaInput;
use Bitmovin\api\model\inputs\AzureInput;
use Bitmovin\api\model\inputs\FtpInput;
use Bitmovin\api\model\inputs\GcsInput;
use Bitmovin\api\model\inputs\HttpInput;
use Bitmovin\api\model\inputs\HttpsInput;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\RtmpInput;
use Bitmovin\api\model\inputs\S3Input;
use Bitmovin\api\model\inputs\SftpInput;
use Bitmovin\api\resource\inputs\AsperaInputResource;
use Bitmovin\api\resource\inputs\AzureInputResource;
use Bitmovin\api\resource\inputs\FtpInputResource;
use Bitmovin\api\resource\inputs\GcsInputResource;
use Bitmovin\api\resource\inputs\HttpInputResource;
use Bitmovin\api\resource\inputs\HttpsInputResource;
use Bitmovin\api\resource\inputs\RtmpInputResource;
use Bitmovin\api\resource\inputs\S3InputResource;
use Bitmovin\api\resource\inputs\SftpInputResource;
use Bitmovin\api\util\ApiUrls;

class InputContainer
{
    private $aspera;
    private $http;
    private $https;
    private $s3;
    private $gcs;
    private $azure;
    private $rtmp;
    private $ftp;
    private $sftp;

    /**
     * InputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->aspera = new AsperaInputResource(ApiUrls::INPUT_ASPERA, AsperaInput::class, $apiKey);
        $this->azure = new AzureInputResource(ApiUrls::INPUT_AZURE, AzureInput::class, $apiKey);
        $this->ftp = new FtpInputResource(ApiUrls::INPUT_FTP, FtpInput::class, $apiKey);
        $this->gcs = new GcsInputResource(ApiUrls::INPUT_GCS, GcsInput::class, $apiKey);
        $this->http = new HttpInputResource(ApiUrls::INPUT_HTTP, HttpInput::class, $apiKey);
        $this->https = new HttpsInputResource(ApiUrls::INPUT_HTTPS, HttpsInput::class, $apiKey);
        $this->rtmp = new RtmpInputResource(ApiUrls::INPUT_RTMP, RtmpInput::class, $apiKey);
        $this->s3 = new S3InputResource(ApiUrls::INPUT_S3, S3Input::class, $apiKey);
        $this->sftp = new SftpInputResource(ApiUrls::INPUT_SFTP, SftpInput::class, $apiKey);
    }

    /**
     * Creates an input
     * @param Input $input
     * @return Input
     */
    public function create(Input $input)
    {
        if ($input instanceof AsperaInput)
        {
            return $this->aspera()->create($input);
        }
        if ($input instanceof AzureInput)
        {
            return $this->azure()->create($input);
        }
        if ($input instanceof FtpInput)
        {
            return $this->ftp()->create($input);
        }
        if ($input instanceof GcsInput)
        {
            return $this->gcs()->create($input);
        }
        if ($input instanceof HttpInput)
        {
            return $this->http()->create($input);
        }
        if ($input instanceof HttpsInput)
        {
            return $this->https()->create($input);
        }
        if ($input instanceof S3Input)
        {
            return $this->s3()->create($input);
        }
        if ($input instanceof SftpInput)
        {
            return $this->sftp()->create($input);
        }
        if ($input instanceof RtmpInput)
        {
            return $this->rtmp()->listPage()[0];
        }
        throw new \InvalidArgumentException();
    }

    /**
     * @return AsperaInputResource
     */
    public function aspera()
    {
        return $this->aspera;
    }

    /**
     * @return HttpInputResource
     */
    public function http()
    {
        return $this->http;
    }

    /**
     * @return HttpsInputResource
     */
    public function https()
    {
        return $this->https;
    }

    /**
     * @return S3InputResource
     */
    public function s3()
    {
        return $this->s3;
    }

    /**
     * @return GcsInputResource
     */
    public function gcs()
    {
        return $this->gcs;
    }

    /**
     * @return AzureInputResource
     */
    public function azure()
    {
        return $this->azure;
    }

    /**
     * @return RtmpInputResource
     */
    public function rtmp()
    {
        return $this->rtmp;
    }

    /**
     * @return FtpInputResource
     */
    public function ftp()
    {
        return $this->ftp;
    }

    /**
     * @return SftpInputResource
     */
    public function sftp()
    {
        return $this->sftp;
    }

}