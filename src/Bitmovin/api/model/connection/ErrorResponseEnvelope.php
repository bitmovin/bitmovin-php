<?php

namespace Bitmovin\api\model\connection;

use Bitmovin\api\enum\ResponseStatus;

class ErrorResponseEnvelope extends ResponseEnvelope
{
    /** @var  ErrorResponseData */
    private $data;

    public function __construct($requestId, ErrorResponseData $errorData, \stdClass $more = NULL)
    {
        parent::__construct($requestId, ResponseStatus::ERROR, $errorData, $more);

        $this->setData($errorData);
    }

    /**
     * @param ErrorResponseData|NULL $data
     */
    private function setData(ErrorResponseData $data = NULL)
    {
        $this->data = $data;
    }

    /**
     * @return ErrorResponseData
     */
    public function getData()
    {
        return $this->data;
    }
}