<?php
/**
 * @Author	Freek Lijten
 */

error_log('made it to request token doc');
require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
new AutoLoader();

$Provider 	= new OAuthProviderWrapper(OAuthProviderWrapper::TOKEN_REQUEST);
$response 	= $Provider->checkOAuthRequest();
if ($response !== true) {
    error_log('response was not true');
	echo $response;
	exit;
}

try {
    error_log('output request token');
	$Provider->outputRequestToken();
} catch (ProviderException $Exception) {
	echo $Exception->getMessage();
}
//exit;
?>
