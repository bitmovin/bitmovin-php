<?php


namespace Bitmovin\input;


class FtpInput extends AbstractInput
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $passive = false;

    /**
     * FtpInput constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

}