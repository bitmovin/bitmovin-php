<?php


namespace Bitmovin\configs\filter;


class DeinterlaceFilterConfig extends AbstractFilterConfig
{

    /**
     * @var string
     */
    public $mode = '';
    /**
     * @var string
     */
    public $parity = '';

    /**
     * DeinterlaceFilterConfig constructor.
     * @param string $mode
     * @param string $parity
     */
    public function __construct($mode, $parity)
    {
        $this->mode = $mode;
        $this->parity = $parity;
    }

}