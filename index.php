<?php
require_once('vendor/autoload.php');

use GuzzleHttp\Client;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;
use GuzzleHttp\Exception\ClientException;

$tenant = "<tenant_id>";
$clientId = "<client_id>";
$clientAssertion = "<token_here>";

//////////////////////////////////////////////////
// Get access token using guzzlehttp/guzzle     //
//////////////////////////////////////////////////

$client = new Client();

try {
    $res = $client->request('POST', "https://login.microsoftonline.com/$tenant/oauth2/v2.0/token", [
        'form_params' => [
            'client_id' => $clientId,
            'scope' => "https://graph.microsoft.com/.default",
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $clientAssertion,
            'grant_type' => 'client_credentials'
        ]
    ]);
} catch (ClientException $exception) {
    die($exception->getResponse()->getBody());
}

$resBody = json_decode($res->getBody()->getContents());
$accessToken = $resBody->access_token;

//////////////////////////////////////////////////
// Get user using microsoft/microsoft-graph     //
//////////////////////////////////////////////////

$graph = new Graph();
$graph->setAccessToken($accessToken);

// Example usage: get user profile and get it's principal name.

try {
    $user = $graph->createRequest('GET', '/users/<user_id>')
        ->setReturnType(User::class)
        ->execute();

    echo $user->getUserPrincipalName();
} catch (ClientException $exception) {
    die($exception);
}
