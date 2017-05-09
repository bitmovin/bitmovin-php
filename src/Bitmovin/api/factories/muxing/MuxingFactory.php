<?php

namespace Bitmovin\api\factories\muxing;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\CodecConfigContainer;
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
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\manifest\AbstractHlsOutput;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsFMP4OutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\manifest\ProgressiveMp4OutputFormat;
use Bitmovin\configs\manifest\SmoothStreamingOutputFormat;

class MuxingFactory
{

    /**
     * @param Encoding  $encoding
     * @param Stream    $stream
     * @param           $output
     * @param           $outputPath
     * @param ApiClient $apiClient
     *
     * @return FMP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
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

    /**
     * @param Encoding                    $encoding
     * @param Stream                      $stream
     * @param                             $output
     * @param                             $outputPath
     * @param SmoothStreamingOutputFormat $smoothStreamingOutputFormat
     * @param ApiClient                   $apiClient
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     *
     */
    private static function createSmoothStreamingMP4Muxing(Encoding $encoding, Stream $stream, $output, $outputPath, SmoothStreamingOutputFormat $smoothStreamingOutputFormat, ApiClient $apiClient)
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
        $muxing->setFilename($smoothStreamingOutputFormat->mediaFileName);
        $muxing->setFragmentDuration(4000);
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);

        return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($muxing);
    }

    /**
     * @param Encoding                    $encoding
     * @param array                       $streams
     * @param                             $output
     * @param                             $outputPath
     * @param ProgressiveMp4OutputFormat  $progressiveMp4OutputFormat
     * @param ApiClient                   $apiClient
     *
     * @return MP4Muxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    private static function createMP4Muxing(Encoding $encoding, array $streams, $output, $outputPath, ProgressiveMp4OutputFormat $progressiveMp4OutputFormat, ApiClient $apiClient)
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
        $muxing->setFilename($progressiveMp4OutputFormat->fileName);
        $muxingStreams = array();
        foreach ($streams as $stream)
        {
            $streamMuxing = new MuxingStream();
            $streamMuxing->setStreamId($stream->getId());
            $muxingStreams[] = $streamMuxing;
        }
        $muxing->setStreams($muxingStreams);

        return $apiClient->encodings()->muxings($encoding)->mp4Muxing()->create($muxing);
    }

    /**
     * @param Encoding  $encoding
     * @param Stream    $stream
     * @param Output    $output
     * @param           $outputPath
     * @param ApiClient $apiClient
     * @param float     $segmentLength
     *
     * @return TSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    private static function createTSMuxing(Encoding $encoding, Stream $stream, Output $output, $outputPath, ApiClient $apiClient, $segmentLength = 4.0)
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
        $muxing->setSegmentLength($segmentLength);
        $streamMuxing = new MuxingStream();
        $streamMuxing->setStreamId($stream->getId());
        $muxing->setStreams([$streamMuxing]);

        return $apiClient->encodings()->muxings($encoding)->tsMuxing()->create($muxing);
    }

    /**
     * @param CodecConfigContainer $codecConfigContainer
     * @param AbstractHlsOutput    $abstractHlsOutput
     * @return bool
     */
    private static function shouldMuxingForHlsOutputBeCreated(CodecConfigContainer $codecConfigContainer, $abstractHlsOutput)
    {
        if ($abstractHlsOutput == null)
            return false;
        if ($abstractHlsOutput->includedStreamConfigs == null)
            return true;
        /** @var AbstractStreamConfig $streamConfig */
        foreach ($abstractHlsOutput->includedStreamConfigs as $streamConfig)
        {
            if ($codecConfigContainer->codecConfig == $streamConfig)
                return true;
        }
        return false;
    }

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param ApiClient         $apiClient
     *
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public static function createMuxingForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, ApiClient $apiClient)
    {
        /** @var DashOutputFormat $dashOutputFormat */
        $dashOutputFormat = null;

        /** @var HlsFMP4OutputFormat $hlsFMP4OutputFormat */
        $hlsFMP4OutputFormat = null;

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
            if ($format instanceof HlsFMP4OutputFormat)
            {
                $hlsFMP4OutputFormat = $format;
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

        static::createMP4MuxingsForEncoding($jobContainer, $encodingContainer, $apiClient);

        foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
        {
            if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
            {
                $fmp4MuxingCreated = false;
                $stream = $codecConfigContainer->stream;
                if (self::shouldMuxingForHlsOutputBeCreated($codecConfigContainer, $hlsFMP4OutputFormat))
                {
                    $codecConfigContainer->muxings[] = $codecConfigContainer->hlsMuxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getDashVideoOutputPath($jobContainer, $hlsFMP4OutputFormat),
                        $apiClient);
                    $fmp4MuxingCreated = true;
                }
                if ($dashOutputFormat)
                {
                    if ($dashOutputFormat->cenc == null)
                    {
                        if (!$fmp4MuxingCreated)
                        {
                            $codecConfigContainer->muxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                                $jobContainer->apiOutput, $codecConfigContainer->getDashVideoOutputPath($jobContainer, $dashOutputFormat),
                                $apiClient);
                        }
                    }
                    else
                    {
                        $muxing = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $apiClient);
                        $muxing->addDrm(DashProtectedManifestFactory::addCencDrmToFmp4Muxing($encodingContainer->encoding, $muxing,
                            $dashOutputFormat->cenc, $jobContainer->apiOutput, $codecConfigContainer->getDashCencVideoOutputPath($jobContainer, $dashOutputFormat),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
                if (self::shouldMuxingForHlsOutputBeCreated($codecConfigContainer, $hlsOutputFormat))
                {
                    $segmentLength = is_numeric($hlsOutputFormat->segmentLength) ? $hlsOutputFormat->segmentLength : 4.0;
                    $codecConfigContainer->muxings[] = $codecConfigContainer->hlsMuxings[] = static::createTSMuxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getHlsVideoOutputPath($jobContainer, $hlsOutputFormat), $apiClient, $segmentLength);
                }
                if ($smoothStreamingOutputFormat)
                {
                    if ($smoothStreamingOutputFormat->playReady == null)
                    {
                        $codecConfigContainer->muxings[] = static::createSmoothStreamingMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getSmoothStreamingVideoOutputPath($jobContainer, $smoothStreamingOutputFormat),
                            $smoothStreamingOutputFormat, $apiClient);
                    }
                    else
                    {
                        $muxing = static::createSmoothStreamingMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $smoothStreamingOutputFormat, $apiClient);
                        $muxing->addDrm(SmoothStreamingManifestFactory::addPlayReadyToMP4Muxing($encodingContainer->encoding, $muxing,
                            $smoothStreamingOutputFormat->playReady, $jobContainer->apiOutput,
                            $codecConfigContainer->getSmoothStreamingPlayReadyVideoOutputPath($jobContainer, $smoothStreamingOutputFormat),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
            }

            if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
            {
                $fmp4MuxingCreated = false;
                $stream = $codecConfigContainer->stream;
                if (self::shouldMuxingForHlsOutputBeCreated($codecConfigContainer, $hlsFMP4OutputFormat))
                {
                    $codecConfigContainer->muxings[] = $codecConfigContainer->hlsMuxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getDashAudioOutputPath($jobContainer, $hlsFMP4OutputFormat), $apiClient);
                    $fmp4MuxingCreated = true;
                }
                if ($dashOutputFormat)
                {
                    if ($dashOutputFormat->cenc == null)
                    {
                        if (!$fmp4MuxingCreated)
                        {
                            $codecConfigContainer->muxings[] = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                                $jobContainer->apiOutput, $codecConfigContainer->getDashAudioOutputPath($jobContainer, $dashOutputFormat), $apiClient);
                        }
                    }
                    else
                    {
                        $muxing = static::createFMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $apiClient);
                        $muxing->addDrm(DashProtectedManifestFactory::addCencDrmToFmp4Muxing($encodingContainer->encoding, $muxing,
                            $dashOutputFormat->cenc, $jobContainer->apiOutput, $codecConfigContainer->getDashCencAudioOutputPath($jobContainer, $dashOutputFormat),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
                if (self::shouldMuxingForHlsOutputBeCreated($codecConfigContainer, $hlsOutputFormat))
                {
                    $segmentLength = is_numeric($hlsOutputFormat->segmentLength) ? $hlsOutputFormat->segmentLength : 4.0;
                    $codecConfigContainer->muxings[] = $codecConfigContainer->hlsMuxings[] = static::createTSMuxing($encodingContainer->encoding, $stream,
                        $jobContainer->apiOutput, $codecConfigContainer->getHlsAudioOutputPath($jobContainer, $hlsOutputFormat), $apiClient, $segmentLength);
                }
                if ($smoothStreamingOutputFormat)
                {
                    if ($smoothStreamingOutputFormat->playReady == null)
                    {
                        $codecConfigContainer->muxings[] = static::createSmoothStreamingMP4Muxing($encodingContainer->encoding, $stream,
                            $jobContainer->apiOutput, $codecConfigContainer->getSmoothStreamingAudioOutputPath($jobContainer, $smoothStreamingOutputFormat),
                            $smoothStreamingOutputFormat, $apiClient);
                    }
                    else
                    {
                        $muxing = static::createSmoothStreamingMP4Muxing($encodingContainer->encoding, $stream,
                            null, null, $smoothStreamingOutputFormat, $apiClient);
                        $muxing->addDrm(SmoothStreamingManifestFactory::addPlayReadyToMP4Muxing($encodingContainer->encoding, $muxing,
                            $smoothStreamingOutputFormat->playReady, $jobContainer->apiOutput,
                            $codecConfigContainer->getSmoothStreamingPlayReadyAudioOutputPath($jobContainer, $smoothStreamingOutputFormat),
                            $apiClient));
                        $codecConfigContainer->muxings[] = $muxing;
                    }
                }
            }
        }
    }

    /**
     * @param JobContainer      $jobContainer
     * @param EncodingContainer $encodingContainer
     * @param ApiClient         $apiClient
     *
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public static function createMP4MuxingsForEncoding(JobContainer $jobContainer, EncodingContainer $encodingContainer, ApiClient $apiClient)
    {
        /** @var ProgressiveMp4OutputFormat[] $progressiveMp4OutputFormats */
        $progressiveMp4OutputFormats = array();

        foreach ($jobContainer->job->outputFormat as $format)
        {
            if ($format instanceof ProgressiveMp4OutputFormat)
            {
                $progressiveMp4OutputFormats[] = $format;
            }
        }
        foreach ($progressiveMp4OutputFormats as $progressiveMp4OutputFormat)
        {
            $streamsToMux = array();
            $streamConfigIds = static::getStreamConfigIdsFromProgressiveMp4Format($progressiveMp4OutputFormat);

            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                if (!in_array($codecConfigContainer->codecConfig->getId(), $streamConfigIds))
                {
                    continue;
                }
                $streamsToMux[] = $codecConfigContainer->stream;
            }

            $tmpCodecConfigContainer = new CodecConfigContainer();
            static::createMP4Muxing($encodingContainer->encoding, $streamsToMux,
                $jobContainer->apiOutput, $tmpCodecConfigContainer->getMp4OutputPath($jobContainer, $progressiveMp4OutputFormat), $progressiveMp4OutputFormat, $apiClient);
        }
    }

    /**
     * @param ProgressiveMp4OutputFormat $progressiveMp4OutputFormat
     *
     * @return array
     */
    private static function getStreamConfigIdsFromProgressiveMp4Format(ProgressiveMp4OutputFormat $progressiveMp4OutputFormat)
    {
        $streamConfigIds = array();
        foreach ($progressiveMp4OutputFormat->streamConfigs as $streamConfig)
        {
            $streamConfigIds[] = $streamConfig->getId();
        }

        return $streamConfigIds;
    }
}