<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\SftpOutput;

class SftpOutputResourceTest extends AbstractOutputResourceTest
{

    /**
     * @return Output
     */
    protected function createOutput()
    {
        $output = new SftpOutput("123", "", "");
        return $output;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->outputs()->sftp();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return SftpOutput::class;
    }
}