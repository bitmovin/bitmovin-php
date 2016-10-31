<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\GcsInput;
use Bitmovin\api\model\inputs\Input;

class GcsInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new GcsInput("test", "123", "234");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->gcs();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return GcsInput::class;
    }
}