<?php
require_once("azureconfig.php");
require_once("vendor/autoload.php");

use \League\OAuth2\Client\Provider\GenericProvider;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;

session_start();
$expectedState = $_SESSION["oauthState"];
unset($_SESSION["oauthState"]);
$providedState = $_GET["state"];

if (!isset($expectedState)) {
    echo "No excepted state in session";
    die;
}

if (!isset($providedState) || $expectedState != $providedState) {
    echo "Invalid auth state<br />The provided auth state did not match the expected value";
    die;
}

$authCode = $_GET["code"];

if (isset($authCode)) {
    $oauthClient = new GenericProvider([
        "clientId"                => $azureConfig["appId"],
        "clientSecret"            => $azureConfig["appSecret"],
        "redirectUri"             => $azureConfig["redirectUri"],
        "urlAuthorize"            => $azureConfig["authority"] . $azureConfig["authorizeEndpoint"],
        "urlAccessToken"          => $azureConfig["authority"] . $azureConfig["tokenEndpoint"],
        "urlResourceOwnerDetails" => "",
        "scopes"                  => $azureConfig["scopes"]
    ]);

    try {
        $accessToken = $oauthClient->getAccessToken("authorization_code", [
            "code" => $authCode
        ]);


        $_SESSION["accessToken"] = $accessToken->getToken();
        header("Location: profile.php");
    } catch (IdentityProviderException $exception) {
        echo $exception->getResponseBody();
        die;
    }
}

if (isset($_GET["error"])) {
    echo $_GET["error"] . "<br />" . $_GET["error_description"];
}
