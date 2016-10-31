<?php

namespace Bitmovin\api\factories\muxing;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\factories\manifest\DashProtectedManifestFactory;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\muxing\FMP4Muxing;
use Bitmovin\api\model\encodings\muxing\MuxingStream;
use Bitmovin\api\model\encodings\muxing\TSMuxing;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\outputs\Output;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;

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
     * @param DashOutputFormat  $dashOutputFormat
     * @param HlsOutputFormat   $hlsOutputFormat
     * @param ApiClient         $apiClient
     */
    public static function createMuxingForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer,
                                                   $dashOutputFormat, $hlsOutputFormat, ApiClient $apiClient)
    {
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
            }
        }
    }

}