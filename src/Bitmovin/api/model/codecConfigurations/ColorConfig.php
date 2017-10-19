<?php

namespace Bitmovin\api\model\codecConfigurations;

use JMS\Serializer\Annotation as JMS;

class ColorConfig
{
    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $copyChromaLocationFlag;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $copyColorSpaceFlag;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $copyColorPrimariesFlag;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $copyColorRangeFlag;

    /**
     * @JMS\Type("boolean")
     * @var  boolean
     */
    private $copyColorTransferFlag;

    /**
     * @JMS\Type("string")
     * @var  string ChromaLocation
     */
    private $chromaLocation;

    /**
     * @JMS\Type("string")
     * @var  string ColorSpace
     */
    private $colorSpace;

    /**
     * @JMS\Type("string")
     * @var  string ColorPrimaries
     */
    private $colorPrimaries;

    /**
     * @JMS\Type("string")
     * @var  string ColorRange
     */
    private $colorRange;

    /**
     * @JMS\Type("string")
     * @var  string ColorTransfer
     */
    private $colorTransfer;

    public function __construct()
    {
        
    }

    /**
     * @return boolean
     */
    public function isCopyChromaLocationFlag()
    {
        return $this->copyChromaLocationFlag;
    }

    /**
     * @param boolean $copyChromaLocationFlag
     */
    public function setCopyChromaLocationFlag($copyChromaLocationFlag)
    {
        $this->copyChromaLocationFlag = $copyChromaLocationFlag;
    }

    /**
     * @return boolean
     */
    public function isCopyColorSpaceFlag()
    {
        return $this->copyColorSpaceFlag;
    }

    /**
     * @param boolean $copyColorSpaceFlag
     */
    public function setCopyColorSpaceFlag($copyColorSpaceFlag)
    {
        $this->copyColorSpaceFlag = $copyColorSpaceFlag;
    }

    /**
     * @return boolean
     */
    public function isCopyColorPrimariesFlag()
    {
        return $this->copyColorPrimariesFlag;
    }

    /**
     * @param boolean $copyColorPrimariesFlag
     */
    public function setCopyColorPrimariesFlag($copyColorPrimariesFlag)
    {
        $this->copyColorPrimariesFlag = $copyColorPrimariesFlag;
    }

    /**
     * @return boolean
     */
    public function isCopyColorRangeFlag()
    {
        return $this->copyColorRangeFlag;
    }

    /**
     * @param boolean $copyColorRangeFlag
     */
    public function setCopyColorRangeFlag($copyColorRangeFlag)
    {
        $this->copyColorRangeFlag = $copyColorRangeFlag;
    }

    /**
     * @return boolean
     */
    public function isCopyColorTransferFlag()
    {
        return $this->copyColorTransferFlag;
    }

    /**
     * @param boolean $copyColorTransferFlag
     */
    public function setCopyColorTransferFlag($copyColorTransferFlag)
    {
        $this->copyColorTransferFlag = $copyColorTransferFlag;
    }

    /**
     * @return string
     */
    public function getChromaLocation()
    {
        return $this->chromaLocation;
    }

    /**
     * @param string $chromaLocation
     */
    public function setChromaLocation($chromaLocation)
    {
        $this->chromaLocation = $chromaLocation;
    }

    /**
     * @return string
     */
    public function getColorSpace()
    {
        return $this->colorSpace;
    }

    /**
     * @param string $colorSpace
     */
    public function setColorSpace($colorSpace)
    {
        $this->colorSpace = $colorSpace;
    }

    /**
     * @return string
     */
    public function getColorPrimaries()
    {
        return $this->colorPrimaries;
    }

    /**
     * @param string $colorPrimaries
     */
    public function setColorPrimaries($colorPrimaries)
    {
        $this->colorPrimaries = $colorPrimaries;
    }

    /**
     * @return string
     */
    public function getColorRange()
    {
        return $this->colorRange;
    }

    /**
     * @param string $colorRange
     */
    public function setColorRange($colorRange)
    {
        $this->colorRange = $colorRange;
    }

    /**
     * @return string
     */
    public function getColorTransfer()
    {
        return $this->colorTransfer;
    }

    /**
     * @param string $colorTransfer
     */
    public function setColorTransfer($mvPredictionMode)
    {
        $this->colorTransfer = $colorTransfer;
    }
}