<?php

namespace Bitmovin\api\factories\manifest;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\drm\PlayReadyEncryptionMethod;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingContentProtection;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingManifest;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingRepresentation;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\drm\PlayReadyDrm;
use Bitmovin\configs\manifest\SmoothStreamingOutputFormat;

class SmoothStreamingManifestFactory
{

    /**
     * @param string                  $encodingId
     * @param string                  $muxingId
     * @param string                  $mediaFile
     * @param SmoothStreamingManifest $manifest
     * @param ApiClient               $apiClient
     * @return SmoothStreamingRepresentation
     */
    private static function addRepresentationToSmoothManifest($encodingId, $muxingId, $mediaFile, $manifest, $apiClient)
    {
        $representation = new SmoothStreamingRepresentation();
        $representation->setEncodingId($encodingId);
        $representation->setMuxingId($muxingId);
        $representation->setMediaFile($mediaFile);
        $representation = $apiClient->manifests()->smooth()->createRepresentation($manifest, $representation);
        return $representation;
    }

    /**
     * @param string                  $encodingId
     * @param string                  $muxingId
     * @param string                  $drmId
     * @param SmoothStreamingManifest $manifest
     * @param ApiClient               $apiClient
     * @return SmoothStreamingRepresentation
     */
    private static function addContentProtectionToSmoothManifest($encodingId, $muxingId, $drmId, $manifest, $apiClient)
    {
        $protection = new SmoothStreamingContentProtection();
        $protection->setEncodingId($encodingId);
        $protection->setDrmId($drmId);
        $protection->setMuxingId($muxingId);
        $protection = $apiClient->manifests()->smooth()->addContentProtection($manifest, $protection);
        return $protection;
    }

    /**
     * @param JobContainer $jobContainer
     * @param MP4Muxing    $muxing
     * @return mixed|string
     */
    private static function createMediaFilePath(JobContainer $jobContainer, $muxing)
    {
        $mediaFilePath = '';
        if (sizeof($muxing->getOutputs()) > 0)
            $mediaFilePath = $muxing->getOutputs()[0]->getOutputPath();
        if (sizeof($muxing->getDrms()) > 0)
            $mediaFilePath = $muxing->getDrms()[0]->getOutputs()[0]->getOutputPath();
        $mediaFilePath = str_ireplace($jobContainer->getOutputPath(), "", $mediaFilePath);
        if (substr($mediaFilePath, 0, 1) == '/')
        {
            $mediaFilePath = substr($mediaFilePath, 1);
        }
        if (substr($mediaFilePath, -1) != '/')
        {
            $mediaFilePath .= '/';
        }
        $mediaFilePath .= $muxing->getFilename();
        return $mediaFilePath;
    }

    /**
     * @param JobContainer                $jobContainer
     * @param SmoothStreamingOutputFormat $smoothStreamingOutputFormat
     * @param EncodingContainer           $encodingContainer
     * @param SmoothStreamingManifest     $manifest
     * @param ApiClient                   $apiClient
     */
    public static function createSmoothStreamingManifestForEncoding(JobContainer $jobContainer, SmoothStreamingOutputFormat $smoothStreamingOutputFormat, EncodingContainer $encodingContainer, $manifest, ApiClient $apiClient)
    {
        /** @var FMP4Muxing $theMuxing */
        $theMuxing = null;
        $theEncoding = null;
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof MP4Muxing)
                    {
                        continue;
                    }
                    if ($theMuxing == null)
                    {
                        $theMuxing = $muxing;
                        $theEncoding = $encodingContainer->encoding;
                    }
                    $mediaFilePath = static::createMediaFilePath($jobContainer, $muxing);
                    static::addRepresentationToSmoothManifest($encodingContainer->encoding->getId(), $muxing->getId(), $mediaFilePath, $manifest, $apiClient);
                }
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                foreach ($codecConfigContainer->muxings as $muxing)
                {
                    if (!$muxing instanceof MP4Muxing)
                    {
                        continue;
                    }
                    if ($theMuxing == null)
                    {
                        $theMuxing = $muxing;
                        $theEncoding = $encodingContainer->encoding;
                    }
                    $mediaFilePath = static::createMediaFilePath($jobContainer, $muxing);
                    static::addRepresentationToSmoothManifest($encodingContainer->encoding->getId(), $muxing->getId(), $mediaFilePath, $manifest, $apiClient);
                }
            }
        }
        if ($theMuxing == null || $smoothStreamingOutputFormat->playReady == null)
            return;
        static::addContentProtectionToSmoothManifest($theEncoding->getId(), $theMuxing->getId(), $theMuxing->getDrms()[0]->getId(), $manifest, $apiClient);
    }

    /**
     * @param Encoding     $encoding
     * @param MP4Muxing    $muxing
     * @param PlayReadyDrm $playReadyDrm
     * @param Output       $output
     * @param string       $outputPath
     * @param ApiClient    $client
     * @return \Bitmovin\api\model\encodings\drms\PlayReadyDrm
     */
    public static function addPlayReadyToMP4Muxing(Encoding $encoding, MP4Muxing $muxing, PlayReadyDrm $playReadyDrm, Output $output, $outputPath, ApiClient $client)
    {

        $encodingOutput = new EncodingOutput($output);
        $encodingOutput->setOutputPath($outputPath);

        $apiPlayReadyDrm = new \Bitmovin\api\model\encodings\drms\PlayReadyDrm([$encodingOutput]);
        $apiPlayReadyDrm->setKeySeed($playReadyDrm->keySeed);
        $apiPlayReadyDrm->setKid($playReadyDrm->kid);
        $apiPlayReadyDrm->setLaUrl($playReadyDrm->laUrl);
        $apiPlayReadyDrm->setPssh($playReadyDrm->pssh);
        $apiPlayReadyDrm->setMethod(PlayReadyEncryptionMethod::PIFF_CTR);

        return $client->encodings()->muxings($encoding)->mp4Muxing()->drm($muxing)->playReadyDrm()->create($apiPlayReadyDrm);
    }

}