<?php

use Bitmovin\api\ApiClient;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new ApiClient('INSERT YOUR API KEY HERE');

$page = 0;
$pageSize = 25;

$items = $client->encodings()->listPage($page * $pageSize, $pageSize);
foreach ($items as $encoding)
{
    $status = $client->encodings()->status($encoding)->getStatus();
    echo "Found encoding with id '" . $encoding->getId() . "', name '" . $encoding->getName() . "', and state '$status'\n";
}