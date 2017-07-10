<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\Status;

require_once __DIR__ . '/../vendor/autoload.php';

// CREATE API CLIENT
$apiClient = new ApiClient('YOUR API KEY');
$encoding = $apiClient->encodings()->getById('ENCODING ID TO STOP');

// STOP LIVE STREAM
$apiClient->encodings()->stopLivestream($encoding);

// WAIT UNTIL LIVE STREAM IS FINISHED
$status = '';
do
{
    sleep(1);
    $status = $apiClient->encodings()->status($encoding)->getStatus();
}
while($status != Status::ERROR && $status != Status::FINISHED);

print 'Live stream has reached final status ' . $status;