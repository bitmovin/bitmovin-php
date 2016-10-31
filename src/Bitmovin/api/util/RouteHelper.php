<?php

namespace Bitmovin\api\util;

class RouteHelper
{
    /**
     * @param string $uriTemplate
     * @param array  $replacements
     *
     * @return string
     */
    public static function buildURI($uriTemplate, array $replacements)
    {
        $uri = $uriTemplate;
        foreach ($replacements as $placeholder => $value)
        {
            $uri = str_replace($placeholder, $value, $uri);
        }

        return $uri;
    }
}