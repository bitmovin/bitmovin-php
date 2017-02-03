<?php


namespace Bitmovin\helper;


class PathHelper
{

    /**
     * @param string[] ...$paths
     * @return string
     */
    public static function combinePath(...$paths)
    {
        $path = '';
        foreach ($paths as $item)
        {
            if ($item === null || strlen($item) == 0)
                continue;
            if (substr($item, 0, 1) != '/' && substr($path, -1) != '/')
            {
                $path .= '/';
            }
            $path .= $item;
        }
        return $path;
    }

}