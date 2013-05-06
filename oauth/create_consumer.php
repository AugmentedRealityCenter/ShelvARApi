<?php
/**
 * @Author	Freek Lijten
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/AutoLoader.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/model/OAuthConsumerModel.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/Configuration.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/OAuthProviderWrapper.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/exceptions/datastore/DataStoreCreateException.php');
//include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/exceptions/datastore/DataStoreCreateException.php');

new AutoLoader();

//create consumer model
$Consumer = new OAuthConsumerModel(Configuration::getDataStore());
//exit; //WORKS 
$Consumer->setConsumerCreateDate(time());
//exit; //WORK
$Consumer->setConsumerKey(OAuthProviderWrapper::generateToken());
//exit; //NO WORK
$Consumer->setConsumerSecret(OAuthProviderWrapper::generateToken());

try {
	$Consumer->save();
} catch (DataStoreCreateException $Exception) {
	echo $Exception->getMessage();
	exit;
}

echo "Consumer key: " . $Consumer->getConsumerKey() . "<br />Consumer secret: " . $Consumer->getConsumerSecret();

?>
