<?php

namespace Bitmovin\configs\drm;


use Bitmovin\api\model\encodings\drms\cencSystems\CencMarlin;
use Bitmovin\configs\drm\cenc\CencPlayReady;
use Bitmovin\configs\drm\cenc\CencWidevine;

class CencDrm
{
    /**
     * @var  string
     */
    private $key;
    /**
     * @var  string
     */
    private $kid;
    /**
     * @var  CencWidevine
     */
    private $widevine;
    /**
     * @var  CencPlayReady
     */
    private $playReady;
    /**
     * @var  CencMarlin
     */
    private $marlin;

    /**
     * CencDrm constructor.
     * @param string                                                $key
     * @param string                                                $kid
     */
    public function __construct($key = '', $kid = '')
    {
        $this->key = $key;
        $this->kid = $kid;
    }


    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
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
     * @return CencWidevine
     */
    public function getWidevine()
    {
        return $this->widevine;
    }

    /**
     * @param CencWidevine $widevine
     */
    public function setWidevine($widevine)
    {
        $this->widevine = $widevine;
    }

    /**
     * @return CencPlayReady
     */
    public function getPlayReady()
    {
        return $this->playReady;
    }

    /**
     * @param CencPlayReady $playReady
     */
    public function setPlayReady($playReady)
    {
        $this->playReady = $playReady;
    }

    /**
     * @return CencMarlin
     */
    public function getMarlin()
    {
        return $this->marlin;
    }

    /**
     * @param CencMarlin $marlin
     */
    public function setMarlin($marlin)
    {
        $this->marlin = $marlin;
    }
}