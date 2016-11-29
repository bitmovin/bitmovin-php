<?php

namespace Bitmovin\api\model\encodings\helper;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class ThumbnailDetails extends AbstractModel
{
    /**
     * @JMS\Type("DateTime")
     * @var  \DateTime
     */
    private $createdAt;

    /**
     * @JMS\Type("DateTime")
     * @var  \DateTime
     */
    private $modifiedAt;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $name;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $height;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $width;

    /**
     * @JMS\Type("array<integer>")
     * @var  array
     */
    private $positions;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $pattern;

    /**
     * @JMS\Type("array<EncodingOutput>")
     * @var  array
     */
    private $outputs;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $modifiedAt
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
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
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param array $positions
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return EncodingOutput[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @param EncodingOutput[] $outputs
     */
    public function setOutputs($outputs)
    {
        $this->outputs = $outputs;
    }
}