<?php


namespace Bitmovin\api\model\inputs;

use Bitmovin\api\ApiClient;

class InputConverterFactory
{

    /**
     * @param \Bitmovin\input\HttpInput $input
     * @return HttpInput|HttpsInput
     */
    public static function createFromHttpInput(\Bitmovin\input\HttpInput $input)
    {
        $url = parse_url($input->url);
        $scheme = $url['scheme'];
        $host = $url['host'];
        $user = '';
        $pass = '';
        if (key_exists('user', $url))
        {
            $user = $url['user'];
        }
        if (key_exists('pass', $url))
        {
            $pass = $url['pass'];
        }
        if ($scheme == 'http')
        {
            $httpInput = new HttpInput($host);
            $httpInput->setUsername($user);
            $httpInput->setPassword($pass);
            return $httpInput;
        }
        else if ($scheme == 'https')
        {
            $httpsInput = new HttpsInput($host);
            $httpsInput->setUsername($user);
            $httpsInput->setPassword($pass);
            return $httpsInput;
        }
        throw new \InvalidArgumentException();
    }

    /**
     * @param \Bitmovin\input\FtpInput $input
     * @return FtpInput
     */
    public static function createFromFtpInput(\Bitmovin\input\FtpInput $input)
    {
        $url = parse_url($input->url);
        $scheme = $url['scheme'];
        $host = $url['host'];
        $user = '';
        $pass = '';
        $port = 21;
        if (key_exists('user', $url))
        {
            $user = $url['user'];
        }
        if (key_exists('pass', $url))
        {
            $pass = $url['pass'];
        }
        if (key_exists('port', $url))
        {
            $port = intval($url['port']);
        }
        if ($scheme == 'ftp')
        {
            $ftpInput = new FtpInput($host, $user, $pass);
            $ftpInput->setPort($port);
            $ftpInput->setPassive($input->passive);
            return $ftpInput;
        }
        throw new \InvalidArgumentException();
    }

    /**
     * @param ApiClient $client
     * @return RtmpInput
     */
    public static function createRtmpInput(ApiClient $client)
    {
        return $client->inputs()->rtmp()->listPage()[0];
    }

    /**
     * @param \Bitmovin\input\S3Input $input
     * @return S3Input
     */
    public static function createFromS3Input(\Bitmovin\input\S3Input $input)
    {
        $s3Input = new S3Input($input->bucket, $input->accessKey, $input->secretKey);
        $s3Input->setCloudRegion($input->cloudRegion);
        return $s3Input;
    }
}