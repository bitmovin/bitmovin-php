<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\SftpInput;

class SftpInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new SftpInput("test", "123", "234");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->sftp();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return SftpInput::class;
    }
}