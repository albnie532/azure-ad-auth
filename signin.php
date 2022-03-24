<?php
require_once("azureconfig.php");
require_once("vendor/autoload.php");

use \League\OAuth2\Client\Provider\GenericProvider;

session_start();

$oauthClient = new GenericProvider([
    "clientId"                => $azureConfig["appId"],
    "clientSecret"            => $azureConfig["appSecret"],
    "redirectUri"             => $azureConfig["redirectUri"],
    "urlAuthorize"            => $azureConfig["authority"] . $azureConfig["authorizeEndpoint"],
    "urlAccessToken"          => $azureConfig["authority"] . $azureConfig["tokenEndpoint"],
    "urlResourceOwnerDetails" => "",
    "scopes"                  => $azureConfig["scopes"],
    "responseType"            => "code"
]);

$authUrl = $oauthClient->getAuthorizationUrl();
$_SESSION["oauthState"] = $oauthClient->getState();

echo "<pre>" . print_r($oauthClient, 1) . "</pre>";
