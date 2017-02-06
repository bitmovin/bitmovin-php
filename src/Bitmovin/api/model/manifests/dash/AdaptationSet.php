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