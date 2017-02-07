<?php

namespace Bitmovin\api\model\manifests\hls;


use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class VttMedia extends AbstractModel
{
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
     * @var string
     */
    private $name;

    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $isDefault;

    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $autoSelect;

    /**
     * @JMS\Type("array<string>")
     * @var string[]
     */
    private $characteristics;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $vttUrl;

    /**
     * @JMS\Type("boolean")
     * @var boolean
     */
    private $forced;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $uri;


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
     * @return bool
     */
    public function isIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return boolean
     */
    public function getAutoSelect()
    {
        return $this->autoSelect;
    }

    /**
     * @param boolean $autoSelect
     */
    public function setAutoSelect($autoSelect)
    {
        $this->autoSelect = $autoSelect;
    }

    /**
     * @return string[]
     */
    public function getCharacteristics()
    {
        return $this->characteristics;
    }

    /**
     * @param string[] $characteristics
     */
    public function setCharacteristics($characteristics)
    {
        $this->characteristics = $characteristics;
    }

    /**
     * @return string
     */
    public function getVttUrl()
    {
        return $this->vttUrl;
    }

    /**
     * @param string $vttUrl
     */
    public function setVttUrl($vttUrl)
    {
        $this->vttUrl = $vttUrl;
    }

    /**
     * @return boolean
     */
    public function getForced()
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


}