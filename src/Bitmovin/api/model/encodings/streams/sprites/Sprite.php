<?php

namespace Bitmovin\api\model\encodings\streams\thumbnails;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use JMS\Serializer\Annotation as JMS;

class Sprite extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var integer
     */
    private $name;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $description;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $height;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $width;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $spriteName;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $vttName;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $distance;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\EncodingOutput>")
     * @var  EncodingOutput[]
     */
    private $outputs;

    /**
     * Sprite constructor.
     * @param integer $width
     * @param integer $height
     * @param string $spriteName
     * @param string $vttName
     */
    public function __construct($width, $height, $spriteName, $vttName)
    {
        $this->setHeight($height);
        $this->setWidth($width);
        $this->setSpriteName($spriteName);
        $this->setVttName($vttName);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param integer $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param integer $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param integer $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return string
     */
    public function getSpriteName()
    {
        return $this->spriteName;
    }

    /**
     * @param string $spriteName
     */
    public function setSpriteName($spriteName)
    {
        $this->spriteName = $spriteName;
    }

    /**
     * @return string
     */
    public function getVttName()
    {
        return $this->vttName;
    }

    /**
     * @param string $vttName
     */
    public function setVttName($vttName)
    {
        $this->vttName = $vttName;
    }

    /**
     * @return \Bitmovin\api\model\encodings\helper\EncodingOutput[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     */
    public function setOutputs($outputs)
    {
        $this->outputs = $outputs;
    }

}