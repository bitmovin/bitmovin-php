<?php

namespace Bitmovin\api\model\connection;

interface IResponse
{
    /**
     * @return string UUID
     */
    public function getRequestId();

    /**
     * @return string(SUCCESS|ERROR)
     */
    public function getStatus();

    /**
     * @return ResponseData
     */
    public function getData();
}