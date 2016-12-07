<?php


namespace Bitmovin;


use Bitmovin\api\ApiClient;
use Bitmovin\api\container\CodecConfigContainer;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\container\TransferContainer;
use Bitmovin\api\container\TransferJobContainer;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\SelectionMode;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\factories\manifest\DashManifestFactory;
use Bitmovin\api\factories\manifest\DashProtectedManifestFactory;
use Bitmovin\api\factories\manifest\HlsManifestFactory;
use Bitmovin\api\factories\manifest\SmoothStreamingManifestFactory;
use Bitmovin\api\factories\muxing\MuxingFactory;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\helper\LiveEncodingDetails;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\thumbnails\Thumbnail;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\InputConverterFactory;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingManifest;
use Bitmovin\api\model\outputs\OutputConverterFactory;
use Bitmovin\api\model\transfers\TransferEncoding;
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\LiveStreamJobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\manifest\SmoothStreamingOutputFormat;
use Bitmovin\configs\TransferConfig;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\FtpInput;
use Bitmovin\input\HttpInput;
use Bitmovin\input\RtmpInput;
use Bitmovin\output\AbstractBitmovinOutput;
use Bitmovin\output\BitmovinAwsOutput;
use Bitmovin\output\BitmovinGcpOutput;
use Bitmovin\output\FtpOutput;
use Bitmovin\output\GcsOutput;
use Bitmovin\output\S3Output;
use Icecave\Parity\Parity;

class BitmovinClient
{
    /**
     * @var string
     */
    private $apiKey;

    /** @var  ApiClient */
    private $apiClient;

    /**
     * BitmovinClient constructor.
     *
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiClient = new ApiClient($apiKey);
    }

    /**
     * @param $stream
     *
     * @return Input|null
     */
    private function convertToApiInput($stream)
    {
        if ($stream->input instanceof HttpInput)
        {
            return InputConverterFactory::createFromHttpInput($stream->input);
        }
        else if ($stream->input instanceof FtpInput)
        {
            return InputConverterFactory::createFromFtpInput($stream->input);
        }
        else if ($stream->input instanceof RtmpInput)
        {
            return InputConverterFactory::createRtmpInput($this->apiClient);
        }

        return null;
    }

    /**
     * @param JobContainer $jobContainer
     *
     */
    private function convertInputsToEncodingContainer(JobContainer $jobContainer)
    {
        $jobContainer->encodingContainers = array();

        $streamConfigs = array_merge($jobContainer->job->encodingProfile->videoStreamConfigs,
            $jobContainer->job->encodingProfile->audioStreamConfigs);

        /** @var AbstractStreamConfig $streamConfig */
        foreach ($streamConfigs as $streamConfig)
        {
            $apiInput = $this->convertToApiInput($streamConfig);
            if ($apiInput == null)
            {
                continue;
            }
            $item = null;
            /** @var EncodingContainer $encodingContainer */
            foreach ($jobContainer->encodingContainers as $encodingContainer)
            {
                if ($encodingContainer->input instanceof $streamConfig->input &&
                    Parity::isEqualTo($encodingContainer->input, $streamConfig->input)
                )
                {
                    $item = $encodingContainer;
                    break;
                }
            }
            if ($item == null)
            {
                $item = new EncodingContainer($this->apiClient, $apiInput, $streamConfig->input);
                $jobContainer->encodingContainers[] = $item;
            }
            $codecConfigContainer = new CodecConfigContainer();
            $codecConfigContainer->codecConfig = $streamConfig;
            $item->codecConfigContainer[] = $codecConfigContainer;
        }
    }

    /**
     * @param JobContainer $jobContainer
     */
    private function createInputs(JobContainer $jobContainer)
    {
        if ($jobContainer->job instanceof LiveStreamJobConfig)
            return;

        /** @var Input[] $inputs */
        $inputs = array();
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            $in_array = false;
            $apiInput = $encodingContainer->apiInput;
            foreach ($inputs as $input)
            {
                if ($input->equals($apiInput))
                {
                    $in_array = true;
                    break;
                }
            }
            if ($in_array)
            {
                continue;
            }
            $encodingContainer->apiInput = $this->apiClient->inputs()->create($apiInput);
        }
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @throws BitmovinException
     */
    private function createOutput(JobContainer $jobContainer)
    {
        $output = $jobContainer->job->output;

        if ($output instanceof AbstractBitmovinOutput)
        {
            $jobContainer->apiOutput = $this->getBitmovinOutputByRegion($output);
            return;
        }

        if ($output instanceof GcsOutput)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromGcsOutput($output));
        }
        else if ($output instanceof FtpOutput)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromFtpOutput($output));
        }
        else if ($output instanceof S3Output)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromS3Output($output));
        }
    }

    /**
     * @param AbstractBitmovinOutput $selectedBitmovinOutput
     *
     * @return \Bitmovin\api\model\outputs\AbstractBitmovinOutput
     * @throws BitmovinException
     */
    private function getBitmovinOutputByRegion(AbstractBitmovinOutput $selectedBitmovinOutput)
    {
        /** @var \Bitmovin\api\model\outputs\AbstractBitmovinOutput[] $bitmovinOutputs */
        $bitmovinOutputs = array();
        $cloudRegionPrefix = "";

        if ($selectedBitmovinOutput instanceof BitmovinAwsOutput)
        {
            $bitmovinOutputs = $this->apiClient->outputs()->bitmovin()->aws()->listPage();
            $cloudRegionPrefix = CloudRegion::AWS_PREFIX;
        }
        else if ($selectedBitmovinOutput instanceof BitmovinGcpOutput)
        {
            $bitmovinOutputs = $this->apiClient->outputs()->bitmovin()->gcp()->listPage();
            $cloudRegionPrefix = CloudRegion::GOOGLE_PREFIX;
        }

        foreach ($bitmovinOutputs as $bitmovinOutput)
        {
            $longCloudRegion = $cloudRegionPrefix . $bitmovinOutput->getCloudRegion();
            if ($longCloudRegion === $selectedBitmovinOutput->cloudRegion)
            {
                return $bitmovinOutput;
            }
        }

        return null;
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @throws BitmovinException
     */
    private function createEncodings(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            $encoding = new Encoding($jobContainer->job->encodingProfile->name);
            $encoding->setEncoderVersion($jobContainer->job->encodingProfile->encoderVersion);
            $encoding->setCloudRegion($jobContainer->job->encodingProfile->cloudRegion);
            $encoding->setDescription($jobContainer->job->encodingProfile->name);
            $encodingContainer->encoding = $this->apiClient->encodings()->create($encoding);
        }
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @throws BitmovinException
     */
    private function createConfigurations(JobContainer $jobContainer)
    {
        // Create H264 configurations
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                if ($codecConfigContainer->codecConfig instanceof H264VideoStreamConfig)
                {
                    /**
                     * @var H264VideoCodecConfiguration
                     */
                    $codec = $codecConfigContainer->codecConfig;
                    $name = $jobContainer->job->encodingProfile->name . '_H264_' .
                        $codec->bitrate . '_' . $codec->width;
                    $config = new H264VideoCodecConfiguration($name, $codec->profile, $codec->bitrate, $codec->rate);
                    $config->setDescription($name);
                    $config->setWidth($codec->width);
                    $config->setHeight($codec->height);
                    $codecConfigContainer->apiCodecConfiguration = $this->apiClient->codecConfigurations()->videoH264()->create($config);
                }
                if ($codecConfigContainer->codecConfig instanceof AudioStreamConfig)
                {
                    /**
                     * @var AudioStreamConfig
                     */
                    $codec = $codecConfigContainer->codecConfig;
                    $name = $jobContainer->job->encodingProfile->name . '_AAC_' .
                        $codec->bitrate . '_' . $codec->rate;
                    $config = new AACAudioCodecConfiguration($name, $codec->bitrate, $codec->rate);
                    $config->setDescription($name);
                    $codecConfigContainer->apiCodecConfiguration = $this->apiClient->codecConfigurations()->audioAAC()->create($config);
                }
            }
        }
    }

    /**
     * @param Encoding           $encoding
     * @param Input              $input
     * @param string             $inputPath
     * @param int                $position
     * @param CodecConfiguration $codecConfiguration
     * @param string             $selectionMode
     *
     * @return Stream
     * @throws BitmovinException
     */
    private function createStream(Encoding $encoding, Input $input, $inputPath, $position, CodecConfiguration $codecConfiguration, $selectionMode)
    {
        $inputStream = new InputStream($input, $inputPath, $selectionMode);
        $inputStream->setPosition($position);

        $stream = new Stream($codecConfiguration, [$inputStream]);
        return $this->apiClient->encodings()->streams($encoding)->create($stream);
    }

    private function createStreams(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                // Create H264 configurations
                if ($codecConfigContainer->apiCodecConfiguration instanceof H264VideoCodecConfiguration)
                {
                    /**
                     * @var H264VideoCodecConfiguration
                     */
                    $codec = $codecConfigContainer->apiCodecConfiguration;
                    $codecConfigContainer->stream = $this->createStream($encodingContainer->encoding, $encodingContainer->apiInput,
                        $encodingContainer->getInputPath(), $codecConfigContainer->codecConfig->position, $codec, SelectionMode::POSITION_ABSOLUTE);
                }
                // Create AAC configurations
                if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
                {
                    /**
                     * @var AACAudioCodecConfiguration
                     */
                    $codec = $codecConfigContainer->apiCodecConfiguration;
                    $codecConfigContainer->stream = $this->createStream($encodingContainer->encoding, $encodingContainer->apiInput,
                        $encodingContainer->getInputPath(), $codecConfigContainer->codecConfig->position, $codec, SelectionMode::POSITION_ABSOLUTE);
                }
            }
        }
    }

    private function createMuxings(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            MuxingFactory::createMuxingForEncoding($jobContainer, $encodingContainer, $this->apiClient);
        }
    }

    private function startEncodings(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            if ($jobContainer->job instanceof LiveStreamJobConfig)
                $this->apiClient->encodings()->startLivestream($encodingContainer->encoding, $jobContainer->job->streamKey);
            else
                $this->apiClient->encodings()->start($encodingContainer->encoding);
        }
    }

    public function stopEncodings(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            if ($jobContainer->job instanceof LiveStreamJobConfig)
                $this->apiClient->encodings()->stopLivestream($encodingContainer->encoding);
            else
                $this->apiClient->encodings()->stop($encodingContainer->encoding);
        }
    }

    public function updateEncodingJobStatus(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            $status = $this->apiClient->encodings()->status($encodingContainer->encoding);
            $encodingContainer->status = $status->getStatus();
        }
    }

    public function updateTransferJobStatus(TransferJobContainer $transferJobContainer)
    {
        foreach ($transferJobContainer->transferContainers as &$transferContainer)
        {
            $status = $this->apiClient->transfers()->encoding()->status($transferContainer->transfer);
            $transferContainer->status = $status->getStatus();
        }
    }

    public function waitForJobsToFinish(JobContainer $jobContainer)
    {
        return $this->waitForJobsToReachState($jobContainer, Status::FINISHED);
    }

    public function waitForJobsToStart(JobContainer $jobContainer)
    {
        return $this->waitForJobsToReachState($jobContainer, Status::RUNNING);
    }

    /**
     * @param JobContainer $jobContainer
     * @param string       $expectedStatus
     */
    private function waitForJobsToReachState(JobContainer $jobContainer, $expectedStatus)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            $status = null;
            while (true)
            {
                $status = $this->apiClient->encodings()->status($encodingContainer->encoding);
                $encodingContainer->status = $status->getStatus();
                if ($status->getStatus() == Status::ERROR || $status->getStatus() == $expectedStatus)
                {
                    break;
                }
                sleep(1);
            }
        }
    }

    public function waitForTransferJobsToFinish(TransferJobContainer $transferJobContainer)
    {
        return $this->waitForTransfersToReachState($transferJobContainer, Status::FINISHED);
    }

    public function waitForTransferJobsToStart(TransferJobContainer $transferJobContainer)
    {
        return $this->waitForTransfersToReachState($transferJobContainer, Status::RUNNING);
    }

    /**
     * @param TransferJobContainer $transferJobContainer
     * @param string               $expectedStatus
     *
     * @throws BitmovinException
     */
    private function waitForTransfersToReachState(TransferJobContainer $transferJobContainer, $expectedStatus)
    {
        foreach ($transferJobContainer->transferContainers as &$transferContainer)
        {
            $status = null;
            while (true)
            {
                $status = $this->apiClient->transfers()->encoding()->get($transferContainer->transfer);
                $transferContainer->status = $status->getState();
                if (strtoupper($status->getState()) == Status::ERROR || strtoupper($status->getState()) == $expectedStatus)
                {
                    break;
                }
                sleep(1);
            }
        }
    }

    private function createDashManifestItem($name, EncodingOutput $output)
    {
        $manifest = new DashManifest();
        $manifest->setManifestName($name);
        $manifest->setOutputs([$output]);
        return $this->apiClient->manifests()->dash()->create($manifest);
    }

    private function addPeriodToDashManifest($start, $duration, DashManifest $manifest)
    {
        $period = new Period();
        $period->setStart($start);
        $period->setDuration($duration);
        return $this->apiClient->manifests()->dash()->createPeriod($manifest, $period);
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @return string
     */
    public function createDashManifest(JobContainer $jobContainer)
    {

        /** @var DashOutputFormat $dash */
        $dash = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof DashOutputFormat)
            {
                $dash = $format;
                break;
            }
        }
        if ($dash == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath());
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $manifest = $this->createDashManifestItem("stream.mpd", $manifestOutput);
        $period = $this->addPeriodToDashManifest("0", null, $manifest);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            if ($dash->cenc != null)
            {
                DashProtectedManifestFactory::createDashManifestForEncoding($jobContainer, $encodingContainer, $manifest, $period, $this->apiClient);
            }
            else
            {
                DashManifestFactory::createDashManifestForEncoding($jobContainer, $encodingContainer, $manifest, $period, $this->apiClient);
            }
        }
        $this->runDashCreation($manifest, $dash);
        return $dash->status;
    }

    private function createHlsManifestItem($name, EncodingOutput $output)
    {
        $manifest = new HlsManifest();
        $manifest->setManifestName($name);
        $manifest->setOutputs([$output]);
        return $this->apiClient->manifests()->hls()->create($manifest);
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @return string
     */
    public function createHlsManifest(JobContainer $jobContainer)
    {
        $hlsFormat = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof HlsOutputFormat)
            {
                $hlsFormat = $format;
                break;
            }
        }
        if ($hlsFormat == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath());
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $manifest = $this->createHlsManifestItem("stream.m3u8", $manifestOutput);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            HlsManifestFactory::createHlsManifestForEncoding($jobContainer, $encodingContainer, $manifest, $this->apiClient);
        }

        $this->runHlsCreation($manifest, $hlsFormat);
        return $hlsFormat->status;
    }

    private function createSmoothManifestItem($name, $serverManifestName, $clientManifestName, EncodingOutput $output)
    {
        $manifest = new SmoothStreamingManifest();
        $manifest->setName($name);
        $manifest->setClientManifestName($clientManifestName);
        $manifest->setServerManifestName($serverManifestName);
        $manifest->setOutputs([$output]);
        return $this->apiClient->manifests()->smooth()->create($manifest);
    }


    /**
     * @param JobContainer $jobContainer
     *
     * @return string
     */
    public function createSmoothStreamingManifest(JobContainer $jobContainer)
    {
        $smoothStreamingFormat = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof SmoothStreamingOutputFormat)
            {
                $smoothStreamingFormat = $format;
                break;
            }
        }
        if ($smoothStreamingFormat == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath());
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $manifest = $this->createSmoothManifestItem($smoothStreamingFormat->manifestName,
            $smoothStreamingFormat->serverManifestName, $smoothStreamingFormat->clientManifestName, $manifestOutput);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            SmoothStreamingManifestFactory::createSmoothStreamingManifestForEncoding($jobContainer, $smoothStreamingFormat, $encodingContainer, $manifest, $this->apiClient);
        }

        $this->runSmoothStreamingCreation($manifest, $smoothStreamingFormat);
        return $smoothStreamingFormat->status;
    }

    private function runDashCreation(DashManifest $manifest, DashOutputFormat $dashOutputFormat)
    {
        $status = null;
        $this->apiClient->manifests()->dash()->start($manifest);
        while (true)
        {
            $status = $this->apiClient->manifests()->dash()->status($manifest);
            $dashOutputFormat->status = $status->getStatus();
            if ($status->getStatus() == Status::ERROR || $status->getStatus() == Status::FINISHED)
            {
                return;
            }
            sleep(1);
        }
    }

    private function runHlsCreation(HlsManifest $manifest, HlsOutputFormat $hlsOutputFormat)
    {
        $status = null;
        $this->apiClient->manifests()->hls()->start($manifest);
        while (true)
        {
            $status = $this->apiClient->manifests()->hls()->status($manifest);
            $hlsOutputFormat->status = $status->getStatus();
            if ($status->getStatus() == Status::ERROR || $status->getStatus() == Status::FINISHED)
            {
                return;
            }
            sleep(1);
        }
    }

    private function runSmoothStreamingCreation(SmoothStreamingManifest $manifest, SmoothStreamingOutputFormat $outputFormat)
    {
        $status = null;
        $this->apiClient->manifests()->smooth()->start($manifest);
        while (true)
        {
            $status = $this->apiClient->manifests()->smooth()->status($manifest);
            $outputFormat->status = $status->getStatus();
            if ($status->getStatus() == Status::ERROR || $status->getStatus() == Status::FINISHED)
            {
                return;
            }
            sleep(1);
        }
    }

    /**
     * @param JobConfig $job
     *
     * @return JobContainer
     * @throws BitmovinException
     */
    public function runJobAndWaitForCompletion(JobConfig $job)
    {
        $jobContainer = $this->startJob($job);
        $this->waitForJobsToFinish($jobContainer);
        $this->createDashManifest($jobContainer);
        $this->createHlsManifest($jobContainer);
        $this->createSmoothStreamingManifest($jobContainer);
        return $jobContainer;
    }

    /**
     * @param TransferConfig $transferConfig
     *
     * @return TransferJobContainer
     * @throws BitmovinException
     */
    public function runTransferJobAndWaitForCompletion(TransferConfig $transferConfig)
    {
        $transferJobContainer = $this->startTransferJob($transferConfig);
        $this->waitForTransferJobsToFinish($transferJobContainer);

        return $transferJobContainer;
    }

    /**
     * @param JobConfig $jobConfig
     *
     * @return JobContainer
     * @throws BitmovinException
     */
    public function startJob(JobConfig $jobConfig)
    {
        $jobContainer = new JobContainer();
        $jobContainer->job = $jobConfig;
        $this->convertInputsToEncodingContainer($jobContainer);
        $this->createInputs($jobContainer);
        $this->createOutput($jobContainer);
        $this->createEncodings($jobContainer);
        $this->createConfigurations($jobContainer);
        $this->createStreams($jobContainer);
        $this->createThumbnails($jobContainer);
        $this->createMuxings($jobContainer);
        $this->startEncodings($jobContainer);
        return $jobContainer;
    }

    private function createThumbnails(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            foreach ($encodingContainer->codecConfigContainer as &$codecConfigContainer)
            {
                $streamConfig = $codecConfigContainer->codecConfig;

                if ($streamConfig instanceof H264VideoStreamConfig)
                {
                    foreach ($streamConfig->thumbnailConfigs as $thumbnailConfig)
                    {
                        $thumbnail = new Thumbnail($thumbnailConfig->height, $thumbnailConfig->positions);
                        $thumbnail->setName($thumbnailConfig->name);
                        $thumbnail->setDescription(($thumbnailConfig->description));
                        $thumbnail->setPattern($thumbnailConfig->pattern);

                        $encodingOutput = new EncodingOutput($jobContainer->apiOutput);
                        $encodingOutput->setOutputPath($codecConfigContainer->getThumbnailOutputPath($jobContainer));
                        $encodingOutput->setAcl(array(new Acl(AclPermission::ACL_PUBLIC_READ)));
                        $thumbnail->setOutputs(array($encodingOutput));

                        $codecConfigContainer->thumbnails[] = $this->apiClient
                            ->encodings()
                            ->streams($encodingContainer->encoding)
                            ->thumbnails($codecConfigContainer->stream)
                            ->create($thumbnail);
                    }

                }
            }
        }
    }

    /**
     * @param TransferConfig $transferConfig
     *
     * @return TransferJobContainer
     * @throws BitmovinException
     */
    public function startTransferJob(TransferConfig $transferConfig)
    {
        $transferJobContainer = new TransferJobContainer();
        $transferJobContainer->transferConfig = $transferConfig;

        $this->convertEncodingsToTransferContainer($transferJobContainer);
        $this->createTransferOutput($transferJobContainer);
        $this->startTransfers($transferJobContainer);

        return $transferJobContainer;
    }

    /**
     * @param TransferJobContainer $transferJobContainer
     */
    private function convertEncodingsToTransferContainer(TransferJobContainer $transferJobContainer)
    {
        $jobContainer = $transferJobContainer->transferConfig->jobContainer;

        foreach ($jobContainer->encodingContainers as $encodingContainer)
        {
            $transferJobContainer->transferContainers[] = new TransferContainer($this->apiClient, $encodingContainer->encoding);
        }
    }

    /**
     * @param TransferJobContainer $transferJobContainer
     *
     * @throws BitmovinException
     */
    public function startTransfers(TransferJobContainer $transferJobContainer)
    {

        foreach ($transferJobContainer->transferContainers as &$transferContainer)
        {
            $transferEncoding = new TransferEncoding($transferContainer->encoding);
            $transferOutput = new EncodingOutput($transferJobContainer->apiOutput);
            $transferOutput->setOutputPath($transferContainer->getTransferOutputPath($transferJobContainer));

            $transferEncoding->setOutputs(array($transferOutput));

            $transferContainer->transfer = $this->apiClient->transfers()->encoding()->create($transferEncoding);
        }
    }

    /**
     * @param TransferJobContainer $transferJobContainer
     *
     * @throws BitmovinException
     */
    private function createTransferOutput(TransferJobContainer $transferJobContainer)
    {
        $output = $transferJobContainer->transferConfig->output;

        if ($output instanceof AbstractBitmovinOutput)
        {
            $transferJobContainer->apiOutput = $this->getBitmovinOutputByRegion($output);
            return;
        }

        if ($output instanceof GcsOutput)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromGcsOutput($output));
        }
        else if ($output instanceof FtpOutput)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromFtpOutput($output));
        }
        else if ($output instanceof S3Output)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromS3Output($output));
        }
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @return string
     */
    public function serializeJobContainer(JobContainer $jobContainer)
    {
        return serialize($jobContainer);
    }

    /**
     * @param string $serializedString
     *
     * @return JobContainer
     */
    public function deserializeJobContainer($serializedString)
    {
        return unserialize($serializedString);
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @return array(LiveEncodingDetails)
     * @throws BitmovinException
     */
    public function getLiveStreamDataWhenAvailable(JobContainer $jobContainer)
    {
        $liveEncodingDetailsArray = array();

        foreach ($jobContainer->encodingContainers as $encodingContainer)
        {
            array_push($liveEncodingDetailsArray, $this->getLiveStreamDataForEncodingWhenAvailable($encodingContainer->encoding));
        }
        return $liveEncodingDetailsArray;
    }

    /**
     * @param Encoding $encoding
     *
     * @return LiveEncodingDetails|null
     * @throws BitmovinException
     */
    private function getLiveStreamDataForEncodingWhenAvailable(Encoding $encoding)
    {
        $liveEncodingDetails = null;

        while (true)
        {
            try
            {
                $liveEncodingDetails = $this->apiClient->encodings()->getLivestreamDetails($encoding);
                break;
            }
            catch (BitmovinException $exception)
            {
                if ($exception->getCode() != 400)
                {
                    throw $exception;
                }
            }
            sleep(1);
        }
        return $liveEncodingDetails;
    }
}