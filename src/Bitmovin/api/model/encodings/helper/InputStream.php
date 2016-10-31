<?php

namespace Bitmovin\api\model\encodings\helper;

use Bitmovin\api\model\inputs\Input;
use JMS\Serializer\Annotation as JMS;

class InputStream
{
    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $inputId;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $inputPath;
    /**
     * @JMS\Type("string")
     * @var  string SelectionMode
     */
    private $selectionMode;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $position;

    /**
     * InputStream constructor.
     *
     * @param Input  $input
     * @param string $inputPath     path to your input media file
     * @param string $selectionMode Enum SelectionMode available
     */
    public function __construct(Input $input, $inputPath, $selectionMode)
    {
        $this->inputId = $input->getId();
        $this->inputPath = $inputPath;
        $this->selectionMode = $selectionMode;
    }

    /**
     * @return string
     */
    public function getInputId()
    {
        return $this->inputId;
    }

    /**
     * @param string $inputId
     */
    public function setInputId($inputId)
    {
        $this->inputId = $inputId;
    }

    /**
     * @return string
     */
    public function getInputPath()
    {
        return $this->inputPath;
    }

    /**
     * @param string $inputPath
     */
    public function setInputPath($inputPath)
    {
        $this->inputPath = $inputPath;
    }

    /**
     * @return string
     */
    public function getSelectionMode()
    {
        return $this->selectionMode;
    }

    /**
     * @param string $selectionMode
     */
    public function setSelectionMode($selectionMode)
    {
        $this->selectionMode = $selectionMode;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

}