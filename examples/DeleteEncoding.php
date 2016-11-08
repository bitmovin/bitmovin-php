<?php

use Bitmovin\api\ApiClient;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new ApiClient('INSERT YOUR API KEY HERE');

$encoding = $client->encodings()->getById('INSERT ID OF ENCODING TO DELETE');
$client->encodings()->delete($encoding);
