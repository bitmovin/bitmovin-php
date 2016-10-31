<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\model\outputs\FtpOutput;
use Bitmovin\api\model\outputs\Output;

class FtpOutputResourceTest extends AbstractOutputResourceTest
{

    /**
     * @return Output
     */
    protected function createOutput()
    {
        $output = new FtpOutput("123", "", "");
        return $output;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->outputs()->ftp();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return FtpOutput::class;
    }
}