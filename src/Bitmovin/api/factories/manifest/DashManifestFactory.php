<?php

namespace Bitmovin\api\factories\manifest;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\CodecConfigContainer;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\manifests\dash\AdaptationSet;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\manifest\DashOutputFormat;

class DashManifestFactory
{

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param DashManifest      $manifest
     * @param Period            $period
     * @param ApiClient         $client
     * @param DashOutputFormat  $dashOutputFormat
     */
    public static function createDashManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, DashManifest $manifest, Period $period,
                                                         ApiClient $client, DashOutputFormat $dashOutputFormat)
    {
        $configurations = self::getConfigurationsForEncoding($encodingContainer, $dashOutputFormat);
        $videoAdaptionSet = null;
        $audioAdaptionSet = null;
        foreach ($configurations as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                if ($videoAdaptionSet == null)
                {
                    $videoAdaptionSet = new VideoAdaptationSet();
                    $videoAdaptionSet = $client->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $videoAdaptionSet);
                }
                static::addAdaptionSetToMuxing($jobContainer, $encodingContainer, $manifest, $period, $codecConfigContainer, $videoAdaptionSet, $client);
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                if ($audioAdaptionSet == null)
                {
                    /** @var AudioStreamConfig $codec */
                    $codec = $codecConfigContainer->codecConfig;
                    $audioAdaptionSet = new AudioAdaptationSet();
                    $audioAdaptionSet->setLang($codec->lang);
                    $audioAdaptionSet = $client->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $period, $audioAdaptionSet);
                }
                static::addAdaptionSetToMuxing($jobContainer, $encodingContainer, $manifest, $period, $codecConfigContainer, $audioAdaptionSet, $client);
            }
        }
    }

    /**
     * @param EncodingContainer $encodingContainer
     * @param DashOutputFormat  $dashOutputFormat
     * @return array
     */
    private static function getConfigurationsForEncoding(EncodingContainer $encodingContainer, DashOutputFormat $dashOutputFormat)
    {
        /**
         * CodecConfigContainer[]
         */
        $configurations = array();
        if ($dashOutputFormat->includedStreamConfigs == null)
        {
            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                $configurations[] = $codecConfigContainer;
            }
        }
        else
        {
            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                foreach ($dashOutputFormat->includedStreamConfigs as $streamConfig)
                {
                    if ($streamConfig == $codecConfigContainer->codecConfig)
                        $configurations[] = $codecConfigContainer;
                }
            }
        }
        return $configurations;
    }

    /**
     * @param JobContainer $jobContainer
     * @param FMP4Muxing   $muxing
     * @return string
     */
    private static function createSegmentPath(JobContainer $jobContainer, FMP4Muxing $muxing)
    {
        $segmentPath = $muxing->getOutputs()[0]->getOutputPath();
        $segmentPath = str_ireplace($jobContainer->getOutputPath(), "", $segmentPath);
        if (substr($segmentPath, 0, 1) == '/')
        {
            $segmentPath = substr($segmentPath, 1);
        }
        return $segmentPath;
    }

    /**
     * @param JobContainer         $jobContainer
     * @param EncodingContainer    $encodingContainer
     * @param DashManifest         $manifest
     * @param Period               $period
     * @param CodecConfigContainer $codecConfigContainer
     * @param AdaptationSet        $adaptionSet
     * @param ApiClient            $client
     */
    private static function addAdaptionSetToMuxing(JobContainer $jobContainer, EncodingContainer $encodingContainer, DashManifest $manifest, Period $period, CodecConfigContainer $codecConfigContainer,
                                                   AdaptationSet $adaptionSet, ApiClient $client)
    {
        foreach ($codecConfigContainer->muxings as $muxing)
        {
            if (!$muxing instanceof FMP4Muxing)
            {
                continue;
            }
            $segmentPath = static::createSegmentPath($jobContainer, $muxing);


            $r = new DashRepresentation();
            $r->setType(DashMuxingType::TYPE_TEMPLATE);
            $r->setEncodingId($encodingContainer->encoding->getId());
            $r->setStreamId($codecConfigContainer->stream->getId());
            $r->setMuxingId($muxing->getId());
            $r->setSegmentPath($segmentPath);

            $client->manifests()->dash()->addRepresentationToAdaptationSet($manifest, $period, $adaptionSet, $r);
        }
    }

}