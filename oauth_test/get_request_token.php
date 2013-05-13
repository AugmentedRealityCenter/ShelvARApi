<?php
/**
 * This is a simple test file to verify the state and correctness of the Provider code.
 *
 * @Author	Freek Lijten
 */


include_once($_SERVER['DOCUMENT_ROOT']  . '/oauth_test/config.php');

session_start();

try {
	//error_log("Consumer Key before is: " . $consumerKey);
	$OAuth              = new OAuth($consumerKey, $consumerSecret);
	$tokenInfo          = $OAuth->getRequestToken(
		$requestURL .
		'?oauth_callback=' .
		$callbackURL// .
		//		'&scope=all'
	);
	
	error_log("Post-get RequestToken");
	print_r($tokenInfo);
} catch (Exception $E) {	
	echo "error";
	echo '<pre>';
	var_dump($E->getMessage());
	var_dump($OAuth->getLastResponse());
	var_dump($OAuth->getLastResponseInfo());
	echo '</pre>';
}

if (empty($tokenInfo['oauth_token_secret']) || empty($tokenInfo['oauth_token'])) {
	echo "THIS IS EMPTY!!";
	echo '<pre>';
	var_dump($tokenInfo);
	echo '</pre>';
	exit;
}

$_SESSION['oauth_token_secret'] = $tokenInfo['oauth_token_secret'];

$location = $authorizeURL . '?oauth_token=' . $tokenInfo['oauth_token'];
header('Location: ' . $location);

?>