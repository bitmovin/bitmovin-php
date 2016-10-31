<?php

namespace Bitmovin\configs\drm\cenc;

class CencWidevine
{
    /**
     * @var string
     */
    private $pssh;

    /**
     * CencWidevine constructor.
     * @param string $pssh
     */
    public function __construct($pssh)
    {
        $this->pssh = $pssh;
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

}