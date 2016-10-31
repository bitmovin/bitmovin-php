<?php


namespace Bitmovin\api\model\encodings\drms\cencSystems;

use JMS\Serializer\Annotation as JMS;

class CencPlayReady
{

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $laUrl;

    /**
     * CencWidevine constructor.
     * @param string $laUrl
     */
    public function __construct($laUrl)
    {
        $this->laUrl = $laUrl;
    }

    /**
     * @return string
     */
    public function getLaUrl()
    {
        return $this->laUrl;
    }

    /**
     * @param string $laUrl
     */
    public function setLaUrl($laUrl)
    {
        $this->laUrl = $laUrl;
    }

}