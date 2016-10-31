<?php


namespace Bitmovin\api\enum;


class Status extends AbstractEnum
{

    const CREATED = 'CREATED';
    const QUEUED = 'QUEUED';
    const RUNNING = 'RUNNING';
    const FINISHED = 'FINISHED';
    const ERROR = 'ERROR';

}