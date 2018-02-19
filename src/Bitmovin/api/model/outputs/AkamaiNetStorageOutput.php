<?php


namespace Bitmovin\api\model\outputs;

use JMS\Serializer\Annotation as JMS;

class AkamaiNetStorageOutput extends Output
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
     * AkamaiNetStorageOutput constructor.
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