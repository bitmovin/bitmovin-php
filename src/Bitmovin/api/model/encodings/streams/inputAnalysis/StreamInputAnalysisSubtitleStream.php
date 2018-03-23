<?php

namespace Bitmovin\api\model\encodings\streams\inputAnalysis;

use JMS\Serializer\Annotation as JMS;

class StreamInputAnalysisSubtitleStream
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $id;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $position;

    /**
     * @JMS\Type("integer")
     * @var integer
     */
    private $duration;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $codec;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $language;

    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $hearingImpaired;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getCodec()
    {
        return $this->codec;
    }

    /**
     * @param string $codec
     */
    public function setCodec($codec)
    {
        $this->codec = $codec;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return boolean
     */
    public function getHearingImpaired()
    {
        return $this->hearingImpaired;
    }

    /**
     * @param boolean $hearingImpaired
     */
    public function setHearingImpaired($hearingImpaired)
    {
        $this->hearingImpaired = $hearingImpaired;
    }
}