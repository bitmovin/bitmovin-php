<?php

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\Status;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new ApiClient('INSERT YOUR API KEY HERE');

$page = 0;
$pageSize = 25;

$items = $client->encodings()->listPage($page * $pageSize, $pageSize);
foreach ($items as $encoding)
{
    $status = $client->encodings()->status($encoding)->getStatus();
    if ($status == Status::FINISHED)
    {
        echo "Deleting encoding with id '" . $encoding->getId() . "'\n";
        $client->encodings()->delete($encoding);
    }
}
