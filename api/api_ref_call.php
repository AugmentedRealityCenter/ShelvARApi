<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');

new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();
if ($response != true) {
	//echo $response;
	echo json_encode(array("result"=>"ERROR NOT AUTHENTICATED, PLEASE LOGIN"));
	exit;
}

?>
