<?php


namespace Bitmovin\api\model\manifests\hls;


use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class MediaInfo extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string MediaInfoType enum
     */
    private $type;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $groupId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $language;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $assocLanguage;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $name;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $uri;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $isDefault;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $autoselect;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $forced;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $instreamId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $encodingId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $streamId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $muxingId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $drmId;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $segmentPath;

    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $startSegmentNumber;
    
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $endSegmentNumber;

    /**
     * @JMS\Type("array<string>")
     * @var  string[]
     */
    private $characteristics;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
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
     * @return string
     */
    public function getAssocLanguage()
    {
        return $this->assocLanguage;
    }

    /**
     * @param string $assocLanguage
     */
    public function setAssocLanguage($assocLanguage)
    {
        $this->assocLanguage = $assocLanguage;
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
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return boolean
     */
    public function isIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     */
    public function setDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return boolean
     */
    public function isAutoselect()
    {
        return $this->autoselect;
    }

    /**
     * @param boolean $autoselect
     */
    public function setAutoselect($autoselect)
    {
        $this->autoselect = $autoselect;
    }

    /**
     * @return boolean
     */
    public function isForced()
    {
        return $this->forced;
    }

    /**
     * @param boolean $forced
     */
    public function setForced($forced)
    {
        $this->forced = $forced;
    }

    /**
     * @return string
     */
    public function getInstreamId()
    {
        return $this->instreamId;
    }

    /**
     * @param string $instreamId
     */
    public function setInstreamId($instreamId)
    {
        $this->instreamId = $instreamId;
    }

    /**
     * @return string
     */
    public function getEncodingId()
    {
        return $this->encodingId;
    }

    /**
     * @param string $encodingId
     */
    public function setEncodingId($encodingId)
    {
        $this->encodingId = $encodingId;
    }

    /**
     * @return string
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @param string $streamId
     */
    public function setStreamId($streamId)
    {
        $this->streamId = $streamId;
    }

    /**
     * @return string
     */
    public function getMuxingId()
    {
        return $this->muxingId;
    }

    /**
     * @param string $muxingId
     */
    public function setMuxingId($muxingId)
    {
        $this->muxingId = $muxingId;
    }

    /**
     * @return string
     */
    public function getDrmId()
    {
        return $this->drmId;
    }

    /**
     * @param string $drmId
     */
    public function setDrmId($drmId)
    {
        $this->drmId = $drmId;
    }

    /**
     * @return string
     */
    public function getSegmentPath()
    {
        return $this->segmentPath;
    }

    /**
     * @param string $segmentPath
     */
    public function setSegmentPath($segmentPath)
    {
        $this->segmentPath = $segmentPath;
    }

    /**
     * @return integer
     */
    public function getStartSegmentNumber()
    {
        return $this->startSegmentNumber;
    }

    /**
     * @param integer $segmentPath
     */
    public function setStartSegmentNumber($startSegmentNumber)
    {
        $this->startSegmentNumber = $startSegmentNumber;
    }

    /**
     * @return integer
     */
    public function getEndSegmentNumber()
    {
        return $this->endSegmentNumber;
    }

    /**
     * @param integer $segmentPath
     */
    public function setEndSegmentNumber($endSegmentNumber)
    {
        $this->endSegmentNumber = $endSegmentNumber;
    }

    /**
     * @return \string[]
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * @param \string[] $characteristics
     */
    public function setCharacteristics($characteristics)
    {
        $this->characteristics = $characteristics;
    }

}