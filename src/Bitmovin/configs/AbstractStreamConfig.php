<?php


namespace Bitmovin\configs;


use Bitmovin\input\AbstractInput;

abstract class AbstractStreamConfig
{
    private $id;

    /**
     * @var AbstractInput
     */
    public $input;

    /**
     * @var int
     */
    public $position = 0;

    public function __construct()
    {
        $this->id = uniqid("bitmovin_",true);
    }

    public function getId() {
        return $this->id;
    }
}