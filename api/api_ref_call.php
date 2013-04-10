<?php

require_once('../oauth/lib/AutoLoader.php');
require_once('../oauth/OAuthProviderWrapper.php');

new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_VERIFY	);
$response 	= $Provider->checkOAuthRequest();
if ($response != true) {
	//echo $response;
	echo json_encode(array("result"=>"ERROR NOT AUTHENTICATED, PLEASE LOGIN"));
	exit;
}

?>
