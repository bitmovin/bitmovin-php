<?php


namespace Bitmovin\api\model\manifests\dash;


use Bitmovin\api\model\AbstractModel;

class AdaptationSet extends AbstractModel
{
    /**
     * @JMS\Type("array")
     * @var array[CustomAttribute]
     */
    private $customAttributes;

    /**
     * @JMS\Type("array")
     * @var array[Role]
     */
    private $roles;

    /**
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param array $customAttributes
     */
    public function setCustomAttributes($customAttributes)
    {
        $this->customAttributes = $customAttributes;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}