<?php


namespace Bitmovin\api\model\inputs;

use Bitmovin\api\ApiClient;

class InputConverterFactory
{

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

    public static function createRtmpInput(ApiClient $client)
    {
        return $client->inputs()->rtmp()->listAll()[0];
    }

}