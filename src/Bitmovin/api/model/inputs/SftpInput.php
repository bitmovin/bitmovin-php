<?php


namespace Bitmovin\api\model\inputs;

use JMS\Serializer\Annotation as JMS;


class SftpInput extends Input
{

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $host;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $port;
    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $passive;

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
     * SFtpInput constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function __construct($host, $username, $password)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
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
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return boolean
     */
    public function isPassive()
    {
        return $this->passive;
    }

    /**
     * @param boolean $passive
     */
    public function setPassive($passive)
    {
        $this->passive = $passive;
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