<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\HttpsInput;
use Bitmovin\api\model\inputs\Input;

class HttpsInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new HttpsInput("test");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->https();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return HttpsInput::class;
    }
}