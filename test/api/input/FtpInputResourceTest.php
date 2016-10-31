<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\FtpInput;
use Bitmovin\api\model\inputs\Input;

class FtpInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new FtpInput("test", "123", "234");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->ftp();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return FtpInput::class;
    }
}