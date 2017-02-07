<?php


namespace Bitmovin\api\model\manifests\dash;

use Bitmovin\api\enum\manifests\dash\Role;
use Bitmovin\api\model\AbstractModel;

use JMS\Serializer\Annotation as JMS;

class AdaptationSet extends AbstractModel
{
    /**
     * @JMS\Type("array<Bitmovin\api\model\manifests\dash\CustomAttribute>")
     * @var CustomAttribute[]
     */
    private $customAttributes;

    /**
     * @JMS\Type("array<string>")
     * @var string[] Role enum
     */
    private $roles;

    /**
     * @return CustomAttribute[]
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param CustomAttribute[] $customAttributes
     */
    public function setCustomAttributes($customAttributes)
    {
        $this->customAttributes = $customAttributes;
    }

    /**
     * @return string[] Role enum
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string[] Role enum $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}