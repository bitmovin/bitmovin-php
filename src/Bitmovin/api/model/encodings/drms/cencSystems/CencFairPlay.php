<?php


namespace Bitmovin\api\model\encodings\drms\cencSystems;

use JMS\Serializer\Annotation as JMS;

class CencFairPlay
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $uri;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $iv;

    /**
     * CencWidevine constructor.
     * @param $iv
     * @param $uri
     */
    public function __construct($iv, $uri)
    {
        $this->iv = $iv;
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }

    /**
     * @param string $iv
     */
    public function setIv($iv)
    {
        $this->iv = $iv;
    }
}