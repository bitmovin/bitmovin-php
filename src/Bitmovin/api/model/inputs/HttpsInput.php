<?php

namespace Bitmovin\api\model\inputs;

use JMS\Serializer\Annotation as JMS;

class HttpsInput extends Input
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $host;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $username;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $password;

    /**
     * HttpsInput constructor.
     * @param string $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

}