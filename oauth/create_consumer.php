<?php
/**
 * @Author	Freek Lijten
 */
require_once('lib/AutoLoader.php');
new AutoLoader();

//create consumer model
$Consumer = new OAuthConsumerModel(Configuration::getDataStore());
$Consumer->setConsumerCreateDate(time());
$Consumer->setConsumerKey(OAuthProviderWrapper::generateToken());
$Consumer->setConsumerSecret(OAuthProviderWrapper::generateToken());

try {
	$Consumer->save();
} catch (DataStoreCreateException $Exception) {
	echo $Exception->getMessage();
	exit;
}

echo "Consumer key: " . $Consumer->getConsumerKey() . "<br />Consumer secret: " . $Consumer->getConsumerSecret();