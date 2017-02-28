<?php


namespace Bitmovin\api\model\outputs;

use Bitmovin\api\enum\output\FtpTransferVersion;

class OutputConverterFactory
{

    /**
     * @param \Bitmovin\output\GcsOutput $output
     * @return GcsOutput
     */
    public static function createFromGcsOutput(\Bitmovin\output\GcsOutput $output)
    {
        $convertedOutput = new GcsOutput($output->bucket, $output->accessKey, $output->secretKey);
        $convertedOutput->setCloudRegion($output->cloudRegion);
        return $convertedOutput;
    }

    /**
     * @param \Bitmovin\output\S3Output $output
     * @return S3Output
     */
    public static function createFromS3Output(\Bitmovin\output\S3Output $output)
    {
        $convertedOutput = new S3Output($output->bucket, $output->accessKey, $output->secretKey);
        $convertedOutput->setCloudRegion($output->cloudRegion);
        return $convertedOutput;
    }

    /**
     * @param \Bitmovin\output\GenericS3Output $output
     * @return GenericS3Output
     */
    public static function createFromGenericS3Output(\Bitmovin\output\GenericS3Output $output)
    {
        $convertedOutput = new GenericS3Output($output->bucket, $output->accessKey, $output->secretKey, $output->host, $output->port);
        return $convertedOutput;
    }

    /**
     * @param \Bitmovin\output\FtpOutput $output
     * @return FtpOutput
     */
    public static function createFromFtpOutput(\Bitmovin\output\FtpOutput $output)
    {
        $convertedOutput = new FtpOutput($output->host, $output->username, $output->password);
        $convertedOutput->setPassive($output->passive);
        $convertedOutput->setPort($output->port);
        if ($output->maxConcurrentConnections > 0)
        {
            $convertedOutput->setMaxConcurrentConnections($output->maxConcurrentConnections);
            $convertedOutput->setTransferVersion(FtpTransferVersion::TRANSFER_VERSION_1_1_0);
        }
        return $convertedOutput;
    }

    /**
     * @param \Bitmovin\output\SftpOutput $output
     * @return SftpOutput
     */
    public static function createFromSftpOutput(\Bitmovin\output\SftpOutput $output)
    {
        $convertedOutput = new SftpOutput($output->host, $output->username, $output->password);
        $convertedOutput->setPassive($output->passive);
        $convertedOutput->setPort($output->port);
        return $convertedOutput;
    }

}