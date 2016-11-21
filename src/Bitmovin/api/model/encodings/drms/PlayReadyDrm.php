<?php


namespace Bitmovin\api\model\encodings\drms;

use JMS\Serializer\Annotation as JMS;

class PlayReadyDrm extends AbstractDrm
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $keySeed;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $laUrl;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $kid;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $pssh;
    /**
     * @JMS\Type("string")
     * @var  string PlayReadyEncryptionMethod enum
     */
    private $method;

    /**
     * AbstractDrm constructor.
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     */
    public function __construct(array $outputs)
    {
        parent::__construct($outputs);
    }

    /**
     * @return string
     */
    public function getKeySeed()
    {
        return $this->keySeed;
    }

    /**
     * @param string $keySeed
     */
    public function setKeySeed($keySeed)
    {
        $this->keySeed = $keySeed;
    }

    /**
     * @return string
     */
    public function getLaUrl()
    {
        return $this->laUrl;
    }

    /**
     * @param string $laUrl
     */
    public function setLaUrl($laUrl)
    {
        $this->laUrl = $laUrl;
    }

    /**
     * @return string
     */
    public function getKid()
    {
        return $this->kid;
    }

    /**
     * @param string $kid
     */
    public function setKid($kid)
    {
        $this->kid = $kid;
    }

    /**
     * @return string
     */
    public function getPssh()
    {
        return $this->pssh;
    }

    /**
     * @param string $pssh
     */
    public function setPssh($pssh)
    {
        $this->pssh = $pssh;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

}