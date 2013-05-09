<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');

new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();
if (!is_bool($response) || $response != true) {
	//echo $response;
  exit(json_encode(array("result"=>"ERROR. OAuth token missing or invalid.")));
}

?>
