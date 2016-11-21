<?php

namespace Bitmovin\api\factories\muxing;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\factories\manifest\DashProtectedManifestFactory;
use Bitmovin\api\factories\manifest\SmoothStreamingManifestFactory;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\manifest\SmoothStreamingOutputFormat;

class MuxingFactory
{

    private static function createFMP4Muxing(Encoding $encoding, Stream $stream, $output, $outputPath, ApiClient $apiClient)
    {
        $encodingOutput = null;
        if ($output != null && $outputPath != null)
        {
            $encodingOutput = new EncodingOutput($output);
            $encodingOutput->setOutputPath($outputPath);
            $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
            $encodingOutput->setAcl([$acl]);
        }

        $muxing = new FMP4Muxing();
        if ($encodingOutput != null)
        {
            $muxing->setOutputs([$encodingOutput]);
        }
        $muxing->setSegmentNaming("segment_%number%.m4s");
        $muxing->setSegmentLength(4.0);
        $muxing->setInitSegmentName("init.mp4");
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);
        return $apiClient->encodings()->muxings($encoding)->fmp4Muxing()->create($muxing);
    }

    private static function createMP4Muxing(Encoding $encoding, Stream $stream, $output, $outputPath, SmoothStreamingOutputFormat $smoothStreamingOutputFormat, ApiClient $apiClient)
    {
        $encodingOutput = null;
        if ($output != null && $outputPath != null)
        {
            $encodingOutput = new EncodingOutput($output);
            $encodingOutput->setOutputPath($outputPath);
            $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
            $encodingOutput->setAcl([$acl]);
        }

        $muxing = new MP4Muxing();
        if ($encodingOutput != null)
        {
            $muxing->setOutputs([$encodingOutput]);
        }
        $muxing->setName($smoothStreamingOutputFormat->mediaFileName);
        $muxing->setFragmentDuration(4000);
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);
        return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($muxing);
    }

    private static function createTSMuxing(Encoding $encoding, Stream $stream, Output $output, $outputPath, ApiClient $apiClient)
    {
        $encodingOutput = null;
        if ($output != null && $outputPath != null)
        {
            $encodingOutput = new EncodingOutput($output);
            $encodingOutput->setOutputPath($outputPath);
            $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
            $encodingOutput->setAcl([$acl]);
        }

        $muxing = new TSMuxing();
        if ($encodingOutput != null)
        {
            $muxing->setOutputs([$encodingOutput]);
        }
        $muxing->setSegmentNaming("segment_%number%.ts");
        $muxing->setSegmentLength(4.0);
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);
        return $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($muxing);
    }

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param ApiClient         $apiClient
     */
    public static function createMuxingForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, ApiClient $apiClient)
    {
        /** @var DashOutputFormat $dashOutputFormat */
        $dashOutputFormat = null;

        /** @var HlsOutputFormat $hlsOutputFormat */
        $hlsOutputFormat = null;

        /** @var SmoothStreamingOutputFormat $smoothStreamingOutputFormat */
        $smoothStreamingOutputFormat = null;

        foreach ($jobContainer->job->outputFormat as $format)
        {
            if ($format instanceof DashOutputFormat)
            {
                $dashOutputFormat = $format;
            }
            if ($format instanceof HlsOutputFormat)
            {
                $hlsOutputFormat = $format;
            }
            if ($format instanceof SmoothStreamingOutputFormat)
            {
                $smoothStreamingOutputFormat = $format;
            }
        }
        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            // Create H264 configurations
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                /**
                 * @var Stream
                 */
                $stream = $codecConfigContainer->stream;
                if ($dashOutputFormat)
                {
                    if ($dashOutputFormat->cenc == null)
                    {
                        $codecConfigContainer->muxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getDashVideoOutputPath($jobContainer),
                            $apiClient);
                    }
                    else
                    {
                        $muxing = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $apiClient);
                        $muxing->addDrm(DashProtectedManifestFactory::addCencDrmToFmp4Muxing($encodingContainer->encoding, $muxing,
                            $dashOutputFormat->cenc, $jobContainer->apiOutput, $codecConfigContainer->getDashCencVideoOutputPath($jobContainer),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
                if ($hlsOutputFormat)
                {
                    $codecConfigContainer->muxings[] = static::createTSMuxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getHlsVideoOutputPath($jobContainer), $apiClient);
                }
                if ($smoothStreamingOutputFormat)
                {
                    if ($smoothStreamingOutputFormat->playReady == null)
                    {
                        $codecConfigContainer->muxings[] = static::createMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getSmoothStreamingVideoOutputPath($jobContainer),
                            $smoothStreamingOutputFormat, $apiClient);
                    }
                    else
                    {
                        $muxing = static::createMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $smoothStreamingOutputFormat, $apiClient);
                        $muxing->addDrm(SmoothStreamingManifestFactory::addPlayReadyToMP4Muxing($encodingContainer->encoding, $muxing,
                            $smoothStreamingOutputFormat->playReady, $jobContainer->apiOutput,
                            $codecConfigContainer->getSmoothStreamingPlayReadyVideoOutputPath($jobContainer),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
            }
            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                /**
                 * @var Stream
                 */
                $stream = $codecConfigContainer->stream;
                if ($dashOutputFormat)
                {
                    if ($dashOutputFormat->cenc == null)
                    {
                        $codecConfigContainer->muxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getDashAudioOutputPath($jobContainer), $apiClient);
                    }
                    else
                    {
                        $muxing = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $apiClient);
                        $muxing->addDrm(DashProtectedManifestFactory::addCencDrmToFmp4Muxing($encodingContainer->encoding, $muxing,
                            $dashOutputFormat->cenc, $jobContainer->apiOutput, $codecConfigContainer->getDashCencAudioOutputPath($jobContainer),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
                if ($hlsOutputFormat)
                {
                    $codecConfigContainer->muxings[] = static::createTSMuxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getHlsAudioOutputPath($jobContainer), $apiClient);
                }
                if ($smoothStreamingOutputFormat)
                {
                    if ($smoothStreamingOutputFormat->playReady == null)
                    {
                        $codecConfigContainer->muxings[] = static::createMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getSmoothStreamingAudioOutputPath($jobContainer),
                            $smoothStreamingOutputFormat, $apiClient);
                    }
                    else
                    {
                        $muxing = static::createMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $smoothStreamingOutputFormat, $apiClient);
                        $muxing->addDrm(SmoothStreamingManifestFactory::addPlayReadyToMP4Muxing($encodingContainer->encoding, $muxing,
                            $smoothStreamingOutputFormat->playReady, $jobContainer->apiOutput,
                            $codecConfigContainer->getSmoothStreamingPlayReadyAudioOutputPath($jobContainer),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
            }
        }
    }

}