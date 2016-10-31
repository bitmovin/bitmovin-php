<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\Input;
use Bitmovin\api\model\inputs\S3Input;

class S3InputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new S3Input("123", "345", "test");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->s3();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return S3Input::class;
    }
}