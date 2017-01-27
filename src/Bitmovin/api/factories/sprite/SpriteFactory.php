<?php

namespace Bitmovin\api\factories\sprite;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\factories\helper\EncodingOutputFactory;
use Bitmovin\api\model\encodings\streams\sprites\Sprite;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class SpriteFactory
{
    public static function createSpritesForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, ApiClient $apiClient)
    {
        $apiOutput = $jobContainer->apiOutput;

        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            $streamConfig = $codecConfigContainer->codecConfig;

            if ($streamConfig instanceof AbstractVideoStreamConfig)
            {
                foreach ($streamConfig->spriteConfigs as $spriteConfig)
                {
                    $outputPath = $codecConfigContainer->getSpriteOutputPath($jobContainer);
                    $encodingOutput = EncodingOutputFactory::createPublicEncodingOutput($apiOutput, $outputPath);

                    $sprite = new Sprite($spriteConfig->width, $spriteConfig->height, $spriteConfig->spriteName, $spriteConfig->vttName);
                    $sprite->setName($spriteConfig->name);
                    $sprite->setDescription(($spriteConfig->description));
                    $sprite->setDistance($spriteConfig->distance);
                    $sprite->setOutputs(array($encodingOutput));

                    $codecConfigContainer->sprites[] = $apiClient
                        ->encodings()
                        ->streams($encodingContainer->encoding)
                        ->sprites($codecConfigContainer->stream)
                        ->create($sprite);
                }
            }
        }
    }
}