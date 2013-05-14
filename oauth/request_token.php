<?php
/**
 * @Author	Freek Lijten
 */
		  error_log("rt Get: " . print_r($_GET,true));
		  error_log("rt Post: " . print_r($_POST,true));

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_REQUEST);
$response 	= $Provider->checkOAuthRequest();
if ($response !== true) {
	echo $response;
	exit;
}

try {
	$Provider->outputRequestToken();
} catch (ProviderException $Exception) {
	echo $Exception->getMessage();
}
//exit;
?>