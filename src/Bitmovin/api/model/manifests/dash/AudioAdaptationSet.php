<?php


namespace Bitmovin\api\model\manifests\dash;

use JMS\Serializer\Annotation as JMS;

class AudioAdaptationSet extends AdaptationSet
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $lang;

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }


}