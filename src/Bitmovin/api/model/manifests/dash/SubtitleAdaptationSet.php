<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 03.02.17
 * Time: 11:41
 */

namespace Bitmovin\api\model\manifests\dash;

class SubtitleAdaptationSet extends AdaptationSet
{
    /**
     * @JMS\type("string")
     * @var string
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