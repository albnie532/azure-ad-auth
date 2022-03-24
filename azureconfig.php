<?php
$azureConfig = [
    'appId'             => "89b62594-494a-43b5-bf6f-20a07d7c6da4",
    'appSecret'         => "noO7Q~AZI7xLj3RGJMxLt4-Qh.HZESfDJ7jgH",
    'redirectUri'       => "http://localhost/callback.php",
    'scopes'            => "openid profile offline_access user.read mailboxsettings.read calendars.readwrite",
    'authority'         => "https://login.microsoftonline.com/b8066e9e-2b7d-4f30-aef3-aee34d49fcb4",
    'authorizeEndpoint' => "/oauth2/v2.0/authorize",
    'tokenEndpoint'     => "/oauth2/v2.0/token",
    "responseType"     => "code",
];
