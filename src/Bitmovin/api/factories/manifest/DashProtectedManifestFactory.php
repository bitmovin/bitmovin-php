<?php


namespace Bitmovin\api\factories\manifest;


use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\manifests\dash\DashMuxingType;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\drms\cencSystems\CencMarlin;
use Bitmovin\api\model\encodings\drms\cencSystems\CencPlayReady;
use Bitmovin\api\model\encodings\drms\cencSystems\CencWidevine;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\manifests\dash\AdaptationSet;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\ContentProtection;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\drm\CencDrm;

class DashProtectedManifestFactory
{


    private static function getDashDRMRepresentation($type, $encodingId, $streamId, $muxingId, $drmId, $segmentPath)
    {
        $r = new DashDrmRepresentation();
        $r->setType($type);
        $r->setEncodingId($encodingId);
        $r->setStreamId($streamId);
        $r->setMuxingId($muxingId);
        $r->setDrmId($drmId);
        $r->setSegmentPath($segmentPath);
        return $r;
    }

    private static function addDashDRMRepresentationToAdaptationSet($type, $encodingId, $streamId, $muxingId, $drmId, $segmentPath,
                                                                    DashManifest $manifest, Period $period, AdaptationSet $adaptationSet,
                                                                    ApiClient $apiClient)
    {
        $representation = static::getDashDRMRepresentation($type, $encodingId, $streamId, $muxingId, $drmId, $segmentPath);
        $apiClient->manifests()->dash()->addDrmRepresentationToAdaptationSet($manifest, $period, $adaptationSet, $representation);
    }

    private static function addContentProtectionToAdaptationSet(DashManifest $manifest, Period $period, AdaptationSet $adaptationSet,
                                                                ContentProtection $contentProtection, ApiClient $apiClient)
    {
        return $apiClient->manifests()->dash()->addContentProtectionToAdaptationSet($manifest, $period, $adaptationSet, $contentProtection);
    }


    private static function addAudioAdaptationSetToPeriod(DashManifest $manifest, Period $period, $lang, ApiClient $apiClient)
    {
        $a = new AudioAdaptationSet();
        $a->setLang($lang);
        return $apiClient->manifests()->dash()->addAudioAdaptionSetToPeriod($manifest, $period, $a);
    }

    private static function addVideoAdaptationSetToPeriod(DashManifest $manifest, Period $period, ApiClient $apiClient)
    {
        $a = new VideoAdaptationSet();
        return $apiClient->manifests()->dash()->addVideoAdaptionSetToPeriod($manifest, $period, $a);
    }

    private static function getContentProtection($encodingId, $streamId, $muxingId, $drmId)
    {
        $contentProtection = new ContentProtection();
        $contentProtection->setEncodingId($encodingId);
        $contentProtection->setStreamId($streamId);
        $contentProtection->setMuxingId($muxingId);
        $contentProtection->setDrmId($drmId);
        return $contentProtection;
    }

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param                   $manifest
     * @param                   $period
     * @param ApiClient         $client
     */
    public static function createDashManifestForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, $manifest, $period, ApiClient $client)
    {
        $videoAdaptionSet = null;
        $audioAdaptionSet = null;
        $contentProtectionAddedVideo = false;
        $contentProtectionAddedAudio = false;
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                if ($videoAdaptionSet == null)
                {
                    $videoAdaptionSet = static::addVideoAdaptationSetToPeriod($manifest, $period, $client);
                }
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof FMP4Muxing)
                    {
                        continue;
                    }
                    $segmentPath = $muxing->getDrms()[0]->getOutputs()[0]->getOutputPath();
                    $segmentPath = str_ireplace($jobContainer->getOutputPath(), "", $segmentPath);
                    if (substr($segmentPath, 0, 1) == '/')
                    {
                        $segmentPath = substr($segmentPath, 1);
                    }
                    static::addDashDRMRepresentationToAdaptationSet(DashMuxingType::TYPE_TEMPLATE, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), $muxing->getDrms()[0]->getId(),
                        $segmentPath, $manifest, $period, $videoAdaptionSet, $client);
                    if (!$contentProtectionAddedVideo)
                    {
                        $contentProtection = static::getContentProtection($encodingContainer->encoding->getId(),
                            $codecConfigContainer->stream->getId(), $muxing->getId(), $muxing->getDrms()[0]->getId());
                        static::addContentProtectionToAdaptationSet($manifest, $period, $videoAdaptionSet, $contentProtection, $client);
                        $contentProtectionAddedVideo = true;
                    }
                }
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                if ($audioAdaptionSet == null)
                {
                    /** @var AudioStreamConfig $codec */
                    $codec = $codecConfigContainer->codecConfig;
                    $audioAdaptionSet = static::addAudioAdaptationSetToPeriod($manifest, $period, $codec->lang, $client);
                }
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof FMP4Muxing)
                    {
                        continue;
                    }
                    $segmentPath = $muxing->getDrms()[0]->getOutputs()[0]->getOutputPath();
                    $segmentPath = str_ireplace($jobContainer->getOutputPath(), "", $segmentPath);
                    if (substr($segmentPath, 0, 1) == '/')
                    {
                        $segmentPath = substr($segmentPath, 1);
                    }
                    static::addDashDRMRepresentationToAdaptationSet(DashMuxingType::TYPE_TEMPLATE, $encodingContainer->encoding->getId(),
                        $codecConfigContainer->stream->getId(), $muxing->getId(), $muxing->getDrms()[0]->getId(),
                        $segmentPath, $manifest, $period, $audioAdaptionSet, $client);
                    if (!$contentProtectionAddedAudio)
                    {
                        $contentProtection = static::getContentProtection($encodingContainer->encoding->getId(),
                            $codecConfigContainer->stream->getId(), $muxing->getId(), $muxing->getDrms()[0]->getId());
                        static::addContentProtectionToAdaptationSet($manifest, $period, $audioAdaptionSet, $contentProtection, $client);
                        $contentProtectionAddedAudio = true;
                    }
                }
            }
        }
    }

    public static function addCencDrmToFmp4Muxing(Encoding $encoding, FMP4Muxing $muxing, CencDrm $cencDrm, Output $output, $outputPath, ApiClient $client)
    {

        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);

        $apiCencDrm = new \Bitmovin\api\model\encodings\drms\CencDrm($cencDrm->getKey(), $cencDrm->getKid(), [$encodingOutput]);
        if ($cencDrm->getWidevine() != null)
        {
            $apiCencDrm->setWidevine(new CencWidevine($cencDrm->getWidevine()->getPssh()));
        }
        if ($cencDrm->getPlayReady() != null)
        {
            $apiCencDrm->setPlayReady(new CencPlayReady($cencDrm->getPlayReady()->getLaUrl()));
        }
        if ($cencDrm->getMarlin() != null)
        {
            $apiCencDrm->setMarlin(new CencMarlin());
        }

        return $client->encodings()->muxings($encoding)->fmp4Muxing()->drm($muxing)->cencDrm()->create($apiCencDrm);
    }

}