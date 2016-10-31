<?php


namespace Bitmovin\input;


class HttpInput extends AbstractInput
{
    /**
     * @var string
     */
    public $url;

    /**
     * HttpInput constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

}