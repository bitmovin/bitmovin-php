<?php

namespace Bitmovin\api\factories\helper;

use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\outputs\Output;

class EncodingOutputFactory
{
    /**
     * @param Output $apiOutput
     * @param string $outputPath
     * @param Acl[]  $acl
     * @return EncodingOutput
     */
    public static function createEncodingOutput(Output $apiOutput, $outputPath, array $acl)
    {
        $encodingOutput = new EncodingOutput($apiOutput);
        $encodingOutput->setOutputPath($outputPath);
        $encodingOutput->setAcl($acl);
        return $encodingOutput;
    }

    /**
     * @param Output  $apiOutput
     * @param  string $outputPath
     * @return EncodingOutput
     */
    public static function createPublicEncodingOutput(Output $apiOutput, $outputPath)
    {
        $aclCollection = array(
            new Acl(AclPermission::ACL_PUBLIC_READ)
        );
        return static::createEncodingOutput($apiOutput, $outputPath, $aclCollection);
    }

    /**
     * @param Output $apiOutput
     * @param string $outputPath
     * @return EncodingOutput
     */
    public static function createPrivateEncodingOutput(Output $apiOutput, $outputPath)
    {
        $aclCollection = array(
            new Acl(AclPermission::ACL_PRIVATE)
        );
        return static::createEncodingOutput($apiOutput, $outputPath, $aclCollection);
    }
}