<?php

namespace Bitmovin\api\model\encodings\helper;

use JMS\Serializer\Annotation as JMS;

class EncodingOutput
{
    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $outputId;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $outputPath;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\Acl>")
     * @var  Acl[]
     */
    private $acl;

    /**
     * Output constructor.
     *
     * @param \Bitmovin\api\model\outputs\Output $output
     */
    public function __construct(\Bitmovin\api\model\outputs\Output $output)
    {
        $this->outputId = $output->getId();
    }

    /**
     * @return string
     */
    public function getOutputId()
    {
        return $this->outputId;
    }

    /**
     * @param string $outputId
     */
    public function setOutputId($outputId)
    {
        $this->outputId = $outputId;
    }

    /**
     * @return string
     */
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * @param string $outputPath
     */
    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;
    }

    /**
     * @return Acl[]
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param Acl[] $acl
     */
    public function setAcl($acl)
    {
        $this->acl = $acl;
    }

}