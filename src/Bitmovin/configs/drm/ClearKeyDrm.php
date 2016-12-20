<?php


namespace Bitmovin\configs\drm;


class ClearKeyDrm
{
    /** @var  string */
    public $key;
    /** @var  string */
    public $kid;

    /**
     * ClearKeyDrm constructor.
     * @param string $key
     * @param string $kid
     */
    public function __construct($key, $kid)
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

}