<?php

namespace Bitmovin\api\model\encodings\helper;

use JMS\Serializer\Annotation as JMS;

class Acl
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $scope = "";
    /**
     * @JMS\Type("string")
     * @var  string Enum AclPermission available
     */
    private $permission;

    /**
     * ACL constructor.
     *
     * @param string $permission Enum AclPermission available
     */
    public function __construct($permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

}