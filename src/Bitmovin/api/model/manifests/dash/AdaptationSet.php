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
     * @JMS\Type("array<Bitmovin\api\enum\AbstractEnum\Role>")
     * @var Role[]
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
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role[] $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }
}