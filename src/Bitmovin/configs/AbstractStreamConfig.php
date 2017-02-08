<?php


namespace Bitmovin\configs;


use Bitmovin\api\enum\SelectionMode;
use Bitmovin\configs\filter\AbstractFilterConfig;
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

    /**
     * @var string SelectionMode enum available
     */
    public $selectionMode = SelectionMode::POSITION_ABSOLUTE;

    public function __construct()
    {
        $this->id = uniqid("bitmovin_", true);
    }

    public function getId()
    {
        return $this->id;
    }

}