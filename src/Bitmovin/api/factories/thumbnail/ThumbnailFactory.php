<?php

namespace Bitmovin\api\factories\thumbnail;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\factories\helper\EncodingOutputFactory;
use Bitmovin\api\model\encodings\streams\thumbnails\Thumbnail;
use Bitmovin\configs\video\AbstractVideoStreamConfig;

class ThumbnailFactory
{
    public static function createThumbnailsForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, ApiClient $apiClient)
    {
        $apiOutput = $jobContainer->apiOutput;

        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            $streamConfig = $codecConfigContainer->codecConfig;

            if ($streamConfig instanceof AbstractVideoStreamConfig)
            {
                foreach ($streamConfig->thumbnailConfigs as $thumbnailConfig)
                {
                    $outputPath = $codecConfigContainer->getThumbnailOutputPath($jobContainer, $thumbnailConfig);
                    $encodingOutput = EncodingOutputFactory::createPublicEncodingOutput($apiOutput, $outputPath);

                    $thumbnail = new Thumbnail($thumbnailConfig->height, $thumbnailConfig->positions);
                    $thumbnail->setName($thumbnailConfig->name);
                    $thumbnail->setDescription(($thumbnailConfig->description));
                    $thumbnail->setPattern($thumbnailConfig->pattern);
                    $thumbnail->setUnit($thumbnailConfig->unit);
                    $thumbnail->setOutputs(array($encodingOutput));

                    $codecConfigContainer->thumbnails[] = $apiClient
                        ->encodings()
                        ->streams($encodingContainer->encoding)
                        ->thumbnails($codecConfigContainer->stream)
                        ->create($thumbnail);
                }
            }
        }
    }
}