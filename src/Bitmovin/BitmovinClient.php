<?php


namespace Bitmovin;

use Bitmovin\api\ApiClient;
use Bitmovin\api\container\CodecConfigContainer;
use Bitmovin\api\container\EncodingContainer;
use Bitmovin\api\container\JobContainer;
use Bitmovin\api\container\ManifestContainer;
use Bitmovin\api\container\TransferContainer;
use Bitmovin\api\container\TransferJobContainer;
use Bitmovin\api\enum\AclPermission;
use Bitmovin\api\enum\CloudRegion;
use Bitmovin\api\enum\Status;
use Bitmovin\api\exceptions\BitmovinException;
use Bitmovin\api\factories\filter\FilterFactory;
use Bitmovin\api\factories\manifest\DashManifestFactory;
use Bitmovin\api\factories\manifest\DashProtectedManifestFactory;
use Bitmovin\api\factories\manifest\HlsManifestFactory;
use Bitmovin\api\factories\manifest\SmoothStreamingManifestFactory;
use Bitmovin\api\factories\muxing\MuxingFactory;
use Bitmovin\api\factories\sprite\SpriteFactory;
use Bitmovin\api\factories\thumbnail\ThumbnailFactory;
use Bitmovin\api\model\codecConfigurations\AACAudioCodecConfiguration;
use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\codecConfigurations\H264VideoCodecConfiguration;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\Acl;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use Bitmovin\api\model\encodings\helper\LiveEncodingDetails;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\InputConverterFactory;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingManifest;
use Bitmovin\api\model\outputs\OutputConverterFactory;
use Bitmovin\api\model\transfers\TransferEncoding;
use Bitmovin\api\model\transfers\TransferManifest;
use Bitmovin\configs\AbstractStreamConfig;
use Bitmovin\configs\audio\AudioStreamConfig;
use Bitmovin\configs\JobConfig;
use Bitmovin\configs\LiveStreamJobConfig;
use Bitmovin\configs\manifest\DashOutputFormat;
use Bitmovin\configs\manifest\HlsFMP4OutputFormat;
use Bitmovin\configs\manifest\HlsOutputFormat;
use Bitmovin\configs\manifest\SmoothStreamingOutputFormat;
use Bitmovin\configs\TransferConfig;
use Bitmovin\configs\video\AbstractVideoStreamConfig;
use Bitmovin\configs\video\H264VideoStreamConfig;
use Bitmovin\input\FtpInput;
use Bitmovin\input\GenericS3Input;
use Bitmovin\input\HttpInput;
use Bitmovin\input\RtmpInput;
use Bitmovin\input\S3Input;
use Bitmovin\output\AbstractBitmovinOutput;
use Bitmovin\output\BitmovinAwsOutput;
use Bitmovin\output\BitmovinGcpOutput;
use Bitmovin\output\FtpOutput;
use Bitmovin\output\GcsOutput;
use Bitmovin\output\GenericS3Output;
use Bitmovin\output\S3Output;
use Bitmovin\output\SftpOutput;
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
        else if ($stream->input instanceof GenericS3Input)
        {
            return InputConverterFactory::createFromGenericS3Input($stream->input);
        }
        else if ($stream->input instanceof S3Input)
        {
            return InputConverterFactory::createFromS3Input($stream->input);
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
        else if ($output instanceof SftpOutput)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromSftpOutput($output));
        }
        else if ($output instanceof S3Output)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromS3Output($output));
        }
        else if ($output instanceof GenericS3Output)
        {
            $jobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromGenericS3Output($output));
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
            $encoding->setInfrastructureId($jobContainer->job->encodingProfile->infrastructureId);
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
                        $encodingContainer->getInputPath(), $codecConfigContainer->codecConfig->position, $codec, $codecConfigContainer->codecConfig->selectionMode);
                    $configuration = $codecConfigContainer->codecConfig;
                    if ($configuration instanceof AbstractVideoStreamConfig)
                    {
                        FilterFactory::createFilterForStream($encodingContainer->encoding, $codecConfigContainer->stream, $configuration->filterConfigs, $this->apiClient);
                    }
                }
                // Create AAC configurations
                if ($codecConfigContainer->apiCodecConfiguration instanceof AACAudioCodecConfiguration)
                {
                    /**
                     * @var AACAudioCodecConfiguration
                     */
                    $codec = $codecConfigContainer->apiCodecConfiguration;
                    $codecConfigContainer->stream = $this->createStream($encodingContainer->encoding, $encodingContainer->apiInput,
                        $encodingContainer->getInputPath(), $codecConfigContainer->codecConfig->position, $codec, $codecConfigContainer->codecConfig->selectionMode);
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
            $encodingContainer->statusObject = $status;
            $encodingContainer->status = $status->getStatus();
        }
    }

    public function updateTransferJobStatus(TransferJobContainer $transferJobContainer)
    {
        foreach ($transferJobContainer->transferContainers as &$transferContainer)
        {
            if ($transferContainer->transfer instanceof TransferEncoding)
            {
                $status = $this->apiClient->transfers()->encoding()->status($transferContainer->transfer);
                $transferContainer->statusObject = $status;
                $transferContainer->status = $status->getStatus();
            }
            if ($transferContainer->transfer instanceof TransferManifest)
            {
                $status = $this->apiClient->transfers()->manifest()->status($transferContainer->transfer);
                $transferContainer->statusObject = $status;
                $transferContainer->status = $status->getStatus();
            }
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
                $encodingContainer->statusObject = $status;
                $encodingContainer->status = $status->getStatus();
                if ($status->getStatus() == Status::ERROR || $status->getStatus() == $expectedStatus)
                {
                    break;
                }
                sleep(1);
            }
        }
    }

    /**
     * @param string $encodingId
     *
     * @return string
     */
    public function getStatusOfEncoding($encodingId)
    {
        return $this->apiClient->encodings()->statusById($encodingId)->getStatus();
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
            if ($transferContainer->transferableResource instanceof HlsManifest)
            {
                continue;
            }

            while (true)
            {
                if ($transferContainer->transfer instanceof TransferEncoding)
                {
                    $status = $this->apiClient->transfers()->encoding()->status($transferContainer->transfer);
                }
                else if ($transferContainer->transfer instanceof TransferManifest)
                {
                    $status = $this->apiClient->transfers()->manifest()->status($transferContainer->transfer);
                }
                $transferContainer->status = $status->getStatus();

                if (in_array($status->getStatus(), [Status::ERROR, $expectedStatus]))
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

        /** @var DashOutputFormat $dashOutputFormat */
        $dashOutputFormat = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof DashOutputFormat)
            {
                $dashOutputFormat = $format;
                break;
            }
        }
        if ($dashOutputFormat == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath($dashOutputFormat->folder));
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $dashManifest = $this->createDashManifestItem($dashOutputFormat->name, $manifestOutput);
        $period = $this->addPeriodToDashManifest("0", null, $dashManifest);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            if ($dashOutputFormat->cenc != null)
            {
                DashProtectedManifestFactory::createDashManifestForEncoding($jobContainer, $encodingContainer, $dashManifest, $period, $this->apiClient);
            }
            else
            {
                DashManifestFactory::createDashManifestForEncoding($jobContainer, $encodingContainer, $dashManifest, $period, $this->apiClient, $dashOutputFormat);
            }
        }
        $this->runDashCreation($dashManifest, $dashOutputFormat);
        $jobContainer->manifestContainers[] = new ManifestContainer($this->apiClient, $dashManifest);

        return $dashOutputFormat->status;
    }

    private function createHlsManifestItem($name, EncodingOutput $output)
    {
        $manifest = new HlsManifest();
        $manifest->setManifestName($name);
        $manifest->setOutputs([$output]);
        return $this->apiClient->manifests()->hls()->create($manifest);
    }

    public function createHlsFMP4Manifest(JobContainer $jobContainer)
    {
        $hlsOutputFormat = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof HlsFMP4OutputFormat)
            {
                $hlsOutputFormat = $format;
                break;
            }
        }
        if ($hlsOutputFormat == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath($hlsOutputFormat->folder));
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $manifest = $this->createHlsManifestItem($hlsOutputFormat->name, $manifestOutput);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            HlsManifestFactory::createHlsFMP4ManifestForEncoding($jobContainer, $encodingContainer, $manifest, $this->apiClient, $hlsOutputFormat);
        }

        $this->runHlsFmp4Creation($manifest, $hlsOutputFormat);
        return $hlsOutputFormat->status;
    }

    /**
     * @param JobContainer $jobContainer
     *
     * @return string
     */
    public function createHlsManifest(JobContainer $jobContainer)
    {
        $hlsOutputFormat = null;
        foreach ($jobContainer->job->outputFormat as &$format)
        {
            if ($format instanceof HlsOutputFormat)
            {
                $hlsOutputFormat = $format;
                break;
            }
        }
        if ($hlsOutputFormat == null)
        {
            return Status::ERROR;
        }

        $manifestOutput = new EncodingOutput($jobContainer->apiOutput);
        $manifestOutput->setOutputPath($jobContainer->getOutputPath($hlsOutputFormat->folder));
        $acl = new Acl(AclPermission::ACL_PUBLIC_READ);
        $manifestOutput->setAcl([$acl]);

        $hlsManifest = $this->createHlsManifestItem($hlsOutputFormat->name, $manifestOutput);

        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            HlsManifestFactory::createHlsManifestForEncoding($jobContainer, $encodingContainer, $hlsManifest, $this->apiClient, $hlsOutputFormat);
        }

        $this->runHlsCreation($hlsManifest, $hlsOutputFormat);
        $jobContainer->manifestContainers[] = new ManifestContainer($this->apiClient, $hlsManifest);

        return $hlsOutputFormat->status;
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
        $manifestOutput->setOutputPath($jobContainer->getOutputPath($smoothStreamingFormat->folder));
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

    private function runHlsFmp4Creation(HlsManifest $manifest, HlsFMP4OutputFormat $hlsOutputFormat)
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

        foreach ($jobContainer->encodingContainers as $encodingContainer)
        {
            if ($encodingContainer->status != Status::FINISHED)
            {
                $id = $encodingContainer->encoding->getId();
                $detailedStatusObject = print_r($encodingContainer->statusObject, true);
                throw new BitmovinException("Encoding with id '$id' has not finished successfully. It's current state is '$encodingContainer->status'. Detailed error response:\n$detailedStatusObject");
            }
        }

        $this->createManifests($jobContainer);

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
        $this->createSprites($jobContainer);
        $this->createMuxings($jobContainer);
        $this->startEncodings($jobContainer);
        return $jobContainer;
    }

    private function createThumbnails(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            ThumbnailFactory::createThumbnailsForEncoding($jobContainer, $encodingContainer, $this->apiClient);
        }
    }

    private function createSprites(JobContainer $jobContainer)
    {
        foreach ($jobContainer->encodingContainers as &$encodingContainer)
        {
            SpriteFactory::createSpritesForEncoding($jobContainer, $encodingContainer, $this->apiClient);
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
        $this->convertManifestsToTransferContainer($transferJobContainer);
        $this->createTransferOutput($transferJobContainer);
        $this->startTransfers($transferJobContainer);

        return $transferJobContainer;
    }

    /**
     * @param TransferJobContainer $transferJobContainer
     */
    private function convertManifestsToTransferContainer(TransferJobContainer $transferJobContainer)
    {
        $jobContainer = $transferJobContainer->transferConfig->jobContainer;

        foreach ($jobContainer->manifestContainers as $manifestContainer)
        {
            $transferJobContainer->transferContainers[] = new TransferContainer($this->apiClient, $manifestContainer->manifest);
        }
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
    private function startTransfers(TransferJobContainer $transferJobContainer)
    {
        foreach ($transferJobContainer->transferContainers as &$transferContainer)
        {
            $transferableResource = $transferContainer->transferableResource;
            $transferOutput = new EncodingOutput($transferJobContainer->apiOutput);
            $transferOutput->setOutputPath($transferContainer->getTransferOutputPath($transferJobContainer));

            //TODO implement HLS Manifest transfer support
            if ($transferableResource instanceof Encoding)
            {
                $transferEncoding = new TransferEncoding($transferableResource);
                $transferEncoding->setOutputs(array($transferOutput));
                $transferContainer->transfer = $this->apiClient->transfers()->encoding()->create($transferEncoding);
            }
            else if ($transferableResource instanceof DashManifest || $transferableResource instanceof HlsManifest)
            {
                $transferManifest = new TransferManifest($transferableResource);
                $transferManifest->setOutputs(array($transferOutput));
                $transferContainer->transfer = $this->apiClient->transfers()->manifest()->create($transferManifest);
            }
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
        else if ($output instanceof SftpOutput)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromSftpOutput($output));
        }
        else if ($output instanceof S3Output)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromS3Output($output));
        }
        else if ($output instanceof GenericS3Output)
        {
            $transferJobContainer->apiOutput = $this->apiClient->outputs()->create(OutputConverterFactory::createFromGenericS3Output($output));
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

    /**
     * @param $jobContainer JobContainer
     */
    public function createManifests($jobContainer)
    {
        $this->createDashManifest($jobContainer);
        $this->createHlsManifest($jobContainer);
        $this->createHlsFMP4Manifest($jobContainer);
        $this->createSmoothStreamingManifest($jobContainer);
    }
}