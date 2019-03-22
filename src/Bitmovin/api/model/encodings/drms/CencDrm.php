<?php


namespace Bitmovin\api\model\encodings\drms;

use Bitmovin\api\model\encodings\drms\cencSystems\CencFairplay;
use Bitmovin\api\model\encodings\drms\cencSystems\CencMarlin;
use Bitmovin\api\model\encodings\drms\cencSystems\CencPlayReady;
use Bitmovin\api\model\encodings\drms\cencSystems\CencWidevine;
use JMS\Serializer\Annotation as JMS;

class CencDrm extends AbstractDrm
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $key;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $kid;
    /**
     * @JMS\Type("string")
     * @var  string  CENCIVSize
     */
    private $ivSize;
    /**
     * @JMS\Type("Bitmovin\api\model\encodings\drms\cencSystems\CencWidevine")
     * @var  CencWidevine
     */
    private $widevine;
    /**
     * @JMS\Type("Bitmovin\api\model\encodings\drms\cencSystems\CencPlayReady")
     * @var  CencPlayReady
     */
    private $playReady;
    /**
     * @JMS\Type("Bitmovin\api\model\encodings\drms\cencSystems\CencMarlin")
     * @var  CencMarlin
     */
    private $marlin;

    /**
     * @JMS\Type("Bitmovin\api\model\encodings\drms\cencSystems\CencFairplay")
     * @var  CencFairplay
     */
    private $fairPlay;

    /**
     * CencDrm constructor.
     * @param string                                                $key
     * @param string                                                $kid
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     */
    public function __construct($key = '', $kid = '', array $outputs = [])
    {
        parent::__construct($outputs);
        $this->key = $key;
        $this->kid = $kid;
    }


    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKid()
    {
        return $this->kid;
    }

    /**
     * @param string $kid
     */
    public function setKid($kid)
    {
        $this->kid = $kid;
    }

    /**
     * @return string
     */
    public function getIVSize()
    {
        return $this->ivSize;
    }

    /**
     * @param string $ivSize
     */
    public function setIVSize($ivSize)
    {
        $this->ivSize = $ivSize;
    }

    /**
     * @return CencWidevine
     */
    public function getWidevine()
    {
        return $this->widevine;
    }

    /**
     * @param CencWidevine $widevine
     */
    public function setWidevine($widevine)
    {
        $this->widevine = $widevine;
    }

    /**
     * @return CencPlayReady
     */
    public function getPlayReady()
    {
        return $this->playReady;
    }

    /**
     * @param CencPlayReady $playReady
     */
    public function setPlayReady($playReady)
    {
        $this->playReady = $playReady;
    }

    /**
     * @return CencMarlin
     */
    public function getMarlin()
    {
        return $this->marlin;
    }

    /**
     * @param CencMarlin $marlin
     */
    public function setMarlin($marlin)
    {
        $this->marlin = $marlin;
    }

    /**
     * @return CencFairplay
     */
    public function getFairplay()
    {
        return $this->fairPlay;
    }

    /**
     * @param CencFairplay $fairPlay
     */
    public function setFairplay($fairPlay)
    {
        $this->fairPlay = $fairPlay;
    }
}