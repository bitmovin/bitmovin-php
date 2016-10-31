<?php

namespace Bitmovin\api\model\connection;

use Bitmovin\api\enum\ResponseStatus;

class SuccessResponseEnvelope extends ResponseEnvelope
{
    /** @var  SuccessResponseData */
    private $data;

    public function __construct($requestId, SuccessResponseData $data, \stdClass $more = NULL)
    {
        parent::__construct($requestId, ResponseStatus::SUCCESS, $data, $more);

        $this->data = $data;
    }

    /**
     * @return SuccessResponseData
     */
    public function getData()
    {
        return $this->data;
    }
}