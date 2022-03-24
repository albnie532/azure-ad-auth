<?php
require_once("vendor/autoload.php");

use Microsoft\Graph\Graph;
use GuzzleHttp\Exception\ClientException;

session_start();
$accessToken = $_SESSION["accessToken"];

if (!isset($accessToken)) {
    echo "No access token";
    die;
}

$graph = new Graph();
$graph->setAccessToken($accessToken);

try {
    $profile = $graph->createRequest("GET", "https://graph.microsoft.com/v1.0/me")
        ->execute();

    echo "<pre>" . print_r($profile, 1) . "</pre>";
} catch (ClientException $exception) {
    echo $exception;
}
