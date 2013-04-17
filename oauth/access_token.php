<?php
/**
 * @Author	Freek Lijten
 */
require_once('AutoLoader.php');
include('OAuthProviderWrapper.php');
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