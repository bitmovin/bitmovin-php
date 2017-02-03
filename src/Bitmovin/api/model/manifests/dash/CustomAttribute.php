<?php
/**
 * Created by PhpStorm.
 * User: dmoser
 * Date: 03.02.17
 * Time: 11:44
 */

namespace Bitmovin\api\model\manifests\dash;


class CustomAttribute
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $key;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $value;

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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}