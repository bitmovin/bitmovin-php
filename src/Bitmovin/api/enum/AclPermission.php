<?php

namespace Bitmovin\api\enum;

class AclPermission
{
    const ACL_PUBLIC_READ = 'PUBLIC_READ';
    const ACL_PRIVATE = 'PRIVATE';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}