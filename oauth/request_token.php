<?php
/**
 * @Author	Freek Lijten
 */

error_log(print_r($_GET,1));

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
