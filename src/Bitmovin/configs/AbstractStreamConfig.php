<?php


namespace Bitmovin\configs;


use Bitmovin\input\AbstractInput;

abstract class AbstractStreamConfig
{

    /**
     * @var AbstractInput
     */
    public $input;

    /**
     * @var int
     */
    public $position = 0;


}