<?php
/**
 *
 * Copyright (c) 2011 Freek Lijten <freeklijten@gmail.com
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Freek Lijten nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author	Freek Lijten
 * @license BSD License
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/Configuration.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/model/OAuthConsumerModel.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/model/OAuthRequestTokenModel.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/oauth/model/OAuthNonceModel.php');

class OAuthProviderWrapper
{
	const TOKEN_REQUEST = 0; //try to get a request token
	const TOKEN_ACCESS	= 1; //try to get an access token
	const TOKEN_VERIFY	= 2; //try to verify an access token so an API call can be made

	private $Provider;

	public function __construct($mode)
	{
		$this->Provider = new OAuthProvider();
		$this->Provider->consumerHandler(array($this,'consumerHandler'));
		$this->Provider->timestampNonceHandler(array($this,'timestampNonceHandler'));

		if ($mode == self::TOKEN_REQUEST) {

			$this->Provider->isRequestTokenEndpoint(true);
			//enforce the presence of these parameters
			$this->Provider->addRequiredParameter("oauth_callback");
			$this->Provider->addRequiredParameter("scope");

		} else if ($mode == self::TOKEN_ACCESS) {

			$this->Provider->tokenHandler(array($this,'checkRequestToken'));

		} else if ($mode == self::TOKEN_VERIFY) {

			$this->Provider->tokenHandler(array($this,'checkAccessToken'));

		}
	}

	/**
	 * Uses OAuthProvider->checkOAuthRequest() which initiates the callbacks and checks the signature
	 *
	 * @return bool|string
	 */
	public function checkOAuthRequest()
	{
		try {
		  // Get HTTP/HTTPS (the possible values for this vary from server to server)
		  $uristring = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
		  // Get domain portion
		  $uristring .= '://'.$_SERVER['HTTP_HOST'];
		  // Get path to script
		  $uristring .= $_SERVER['REQUEST_URI'];
		  error_log("uristring: " . $uristring);

		  $this->Provider->checkOAuthRequest($uristring,$_SERVER['HTTP_METHOD']);
		} catch (Exception $Exception) {
			return OAuthProvider::reportProblem($Exception);
		}
		return true;
	}

	/**
	 * Wrapper around OAuthProvider::generateToken to add sha1 hashing at one place
	 * @static
	 * @param 	bool $sha1
	 * @return 	string
	 */
	public static function generateToken()
	{
		$token = OAuthProvider::generateToken(40); //, true
		return sha1( $token );
	}

	/**
	 * Generates and outputs a request token
	 * @throws ProviderException
	 */
	public function outputRequestToken()
	{
		$token 			= OAuthProviderWrapper::generateToken();
		$tokenSecret 	= OAuthProviderWrapper::generateToken();
		$RequestToken 	= new OAuthRequestTokenModel(Configuration::getDataStore());

		$RequestToken->setToken($token);
		$RequestToken->setTokenSecret($tokenSecret);
		$RequestToken->setTokenDate(time());
		$RequestToken->setTokenConsumerKey($this->Provider->consumer_key);

		$headers = apache_request_headers();
		if(isset($_GET['oauth_callback'])){
		  $RequestToken->setTokenCallback($_GET['oauth_callback']);
		} else {	  
		  if(isset($headers['Authorization'])){
		    $pieces = explode(" ",$headers['Authorization']);
		    foreach ($pieces as $piece){
		      if(stripos($piece,"oauth_callback") !== false){
			$breakapart = explode("\"",$piece);
			$RequestToken->setTokenCallback(urldecode($breakapart[1]));
		      }
		    }
		  } else {
		    echo "error: No callback supplied";
		    exit;
		  }
		}

		if(isset($_GET['scope'])){
		  $RequestToken->setTokenScope($_GET['scope']);
		} else {
		  if(isset($headers['Authorization'])){
		    $pieces = explode(" ",$headers['Authorization']);
		    foreach ($pieces as $piece){
		      if(stripos($piece,"scope") !== false){
			$breakapart = explode("\"",$piece);
			$RequestToken->setTokenScope(urldecode($breakapart[1]));
		      }
		    }
		  } 
		}

		try {
			$RequestToken->save();
		} catch (DataStoreCreateException $Exception) {
			throw new ProviderException($Exception->getMessage());
		}

		echo "oauth_token=$token&oauth_token_secret=$tokenSecret&oauth_callback_confirmed=true";
	}

	/**
	 * Tests if the provided RequestToken meets the RFC specs and if so creates and outputs an AccessToken
	 *
	 * @throws ProviderException
	 */
	public function outputAccessToken()
	{
		$DataStore	= Configuration::getDataStore();
		$token 			= OAuthProviderWrapper::generateToken();
		$tokenSecret 	= OAuthProviderWrapper::generateToken();
		$AccessToken 	= new OAuthAccessTokenModel($DataStore);
		$RequestToken	= OAuthRequestTokenModel::loadFromToken($this->Provider->token, $DataStore);

		$AccessToken->setAccessToken($token);
		$AccessToken->setAccessTokenSecret($tokenSecret);

		$db = new database();
		$db->query = "SELECT inst_id FROM users WHERE user_num = ?";
		$user_num = $RequestToken->getTokenUserId();
		$db->params = array($user_num);
		$db->type = 's';
		$result = $db->fetch();

		$res2 = array();

		if(count($result) > 0){
		  $db = new database();
		  $db->query = "SELECT exp_date FROM institutions WHERE inst_id = ?";
		  $inst_id = $result[0]['inst_id'];
		  $db->params = array($inst_id);
		  $db->type = 's';
		  $res2 = $db->fetch();
		}

		$date = time();
		if(count($res2) > 0){
		  $date = new DateTime($res2[0]['exp_date'], new DateTimeZone("UTC"));
		}

		$AccessToken->setAccessTokenDate("" . $date->getTimestamp());

		$AccessToken->setAccessTokenConsumerKey($this->Provider->consumer_key);
		$AccessToken->setAccessTokenUserId($RequestToken->getTokenUserId());
		$AccessToken->setAccessTokenScope($RequestToken->getTokenScope());

		try {
			$AccessToken->save();
		} catch (DataStoreCreateException $Exception) {
			throw new ProviderException($Exception->getMessage());
		}

		//The access token was saved. This means the request token that was exchanged for it can be deleted.
		try {
			$RequestToken->delete();
		} catch (DataStoreDeleteException $Exception) {
			throw new ProviderException($Exception->getMessage());
		}

		//all is well, output token
		echo "oauth_token=$token&oauth_token_secret=$tokenSecret";
	}

	/**
	 * Returns the user Id for the currently authorized user
	 *
	 * @throws ProviderException
	 * @return int
	 */
	public function getUserId()
	{
		try {
			$AccessToken = OAuthAccessTokenModel::loadFromToken($this->Provider->token, Configuration::getDataStore());
		} catch (DataStoreReadException $Exception) {
			throw new ProviderException("Couldn't find a user id corresponding with current token information");
		}
		return $AccessToken->getAccessTokenUserId();
	}

	/**
	 * Returns the expiration date for the current access token
	 *
	 * @throws ProviderException
	 * @return int
	 */
	public function getAccessTokenDate()
	{
		try {
			$AccessToken = OAuthAccessTokenModel::loadFromToken($this->Provider->token, Configuration::getDataStore());
		} catch (DataStoreReadException $Exception) {
			throw new ProviderException("Couldn't find a user id corresponding with current token information");
		}
		return $AccessToken->getAccessTokenDate();
	}


	/**
	 * Checks if the nonce is valid and, if so, stores it in the DataStore.
	 * Used as a callback function
	 *
	 * @param  $Provider
	 * @return int
	 */
	public static function timestampNonceHandler($Provider)
	{
		// Timestamp is off too much (5 mins+), refuse token
		$now = time();
		if ($now - $Provider->timestamp > 300) {
			return OAUTH_BAD_TIMESTAMP;
		}

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_BAD_NONCE;
		}

		if (OAuthNonceModel::nonceExists($Provider->nonce, $DataStore)) {
			return OAUTH_BAD_NONCE;
		}

		$OAuthNonce = new OAuthNonceModel(Configuration::getDataStore());
		$OAuthNonce->setId($Provider->nonce);
		$OAuthNonce->setNonceConsumerKey($Provider->consumer_key);
		$OAuthNonce->setNonceDate(time());

		try {
			$OAuthNonce->save();
		} catch (DataStoreCreateException $Exception) {
			return OAUTH_BAD_NONCE;
		}

		return OAUTH_OK;
	}

	/**
	 * Checks if the provided consumer key is valid and sets the corresponding
	 * consumer secret. Used as a callback function.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function consumerHandler($Provider)
	{
		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			// Ideally this exception should be rethrown here but the internals of PECL's OAuth class throw an exception
			// when a non-accepted return value (or no return value) is received. This seems to be winning from exceptions
			// thrown at this point.
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		try {
			$OAuthConsumer = OAuthConsumerModel::loadFromConsumerKey($Provider->consumer_key, $DataStore);
		} catch (DataStoreReadException $Exception) {
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		}

		$Provider->consumer_secret = $OAuthConsumer->getConsumerSecret();
		return OAUTH_OK;
	}

	/**
	 * Checks if there is token information for the provided token and sets the secret if it can be found.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function checkRequestToken($Provider)
	{
		// Ideally this function should rethrow exceptions, but the internals of PECL's OAuth class
		// Expect one of the OAUTH constants to be returned. When left out an exception is thrown, negating
		// out exception thrown here.

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			return OAUTH_TOKEN_REJECTED;
		}

		//Token can not be loaded, reject it.
		try {
			$RequestToken = OAuthRequestTokenModel::loadFromToken($Provider->token, $DataStore);
		} catch (DataStoreReadException $Exception) {
			return OAUTH_TOKEN_REJECTED;
		}

		//The consumer must be the same as the one this request token was originally issued for
		if ($RequestToken->getTokenConsumerKey() != $Provider->consumer_key) {
			return OAUTH_TOKEN_REJECTED;
		}

		if (!$RequestToken) {
			return OAUTH_TOKEN_REJECTED;
		}

		$headers = apache_request_headers();
		$input_verifier = "";
		if(isset($_GET['oauth_verifier'])){
		  $input_verifier = $_GET['oauth_verifier'];
		}
		if(isset($headers['Authorization'])){
		  $pieces = explode(" ",$headers['Authorization']);
		  foreach ($pieces as $piece){
		    if(stripos($piece,"oauth_verifier") !== false){
		      $breakapart = explode("\"",$piece);
		      $input_verifier = $breakapart[1];
		    }
		  }
		}

		//Check if the verification code is correct.
		if ($input_verifier != $RequestToken->getTokenVerificationCode()) {
			return OAUTH_VERIFIER_INVALID;
		}

		$Provider->token_secret = $RequestToken->getTokenSecret();
		return OAUTH_OK;
	}

	/**
	 * Checks if there is token information for the provided access token and sets the secret if it can be found.
	 *
	 * @static
	 * @param 	$Provider
	 * @return 	int
	 */
	public static function checkAccessToken($Provider)
	{
		// Ideally this function should rethrow exceptions, but the internals of PECL's OAuth class
		// Expect one of the OAUTH constants to be returned. When left out an exception is thrown, negating
		// out exception thrown here.

		try {
			$DataStore = Configuration::getDataStore();
		} catch (DataStoreConnectException $Exception) {
			return OAUTH_TOKEN_REJECTED;
		}

		//Try to load the access token
		try {
			$AccessToken = OAuthAccessTokenModel::loadFromToken($Provider->token, $DataStore);
		} catch (DataStoreReadException $Exception) {
			return OAUTH_TOKEN_REJECTED;
		}

		//The consumer must be the same as the one this request token was originally issued for
		if ($AccessToken->getAccessTokenConsumerKey() != $Provider->consumer_key) {
			return OAUTH_TOKEN_REJECTED;
		}

		$Provider->token_secret = $AccessToken->getAccessTokenSecret();
		return OAUTH_OK;
	}
}
