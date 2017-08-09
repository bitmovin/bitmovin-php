<?php


namespace Bitmovin\output;

class SftpOutput extends AbstractOutput
{

    /**
     * @var string
     */
    public $host;

    /**
     * @var integer
     */
    public $port = 22;

    /**
     * @var boolean
     */
    public $passive;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $prefix = '';

    /**
     * @var string
     */
    public $transferVersion;

    /**
     * @var int
     */
    public $maxConcurrentConnections = 0;

    /**
     * FtpInput constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $prefix
     */
    public function __construct($host, $username = '', $password = '', $prefix = '')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->prefix = $prefix;
    }

}
