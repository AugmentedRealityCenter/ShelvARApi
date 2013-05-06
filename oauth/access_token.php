<?php
/**
 * @Author	Freek Lijten
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
//include_once('OAuthProviderWrapper.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_ACCESS);
$response 	= $Provider->checkOAuthRequest();
if ($response !== true) {
	echo $response;
	exit;
}

try {
	$Provider->outputAccessToken();
} catch (ProviderException $Exception) {
	echo $Exception->getMessage();
}
exit;