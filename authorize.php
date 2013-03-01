<?php
/*
Filename: authorize.php
@author Jake Rego
2/21/2013
Description: This controller asks the user if it allows the consumer 
			to access his account. When allowed then the consumer can
			exchange his request token for an access token.	You have 
			to make sure that an user is logged on when accessing the
			code below.

* Note: The OAuthServer uses the $_SESSION to store some OAuth state, 
		so you must either call session_start() or have automatic session
		start enabled.

Source: code.google.com
*/

// The current user
$user_id = 1;

// Fetch the oauth store and the oauth server.
$store  = OAuthStore::instance();
$server = new OAuthServer();

try
{
    // Check if there is a valid request token in the current request
    // Returns an array with the consumer key, consumer secret, token, token secret and token type.
    $rs = $server->authorizeVerify();

    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        // See if the user clicked the 'allow' submit button (or whatever you choose)
        $authorized = array_key_exists('allow', $_POST);

        // Set the request token to be authorized or not authorized
        // When there was a oauth_callback then this will redirect to the consumer
        $server->authorizeFinish($authorized, $user_id);

        // No oauth_callback, show the user the result of the authorization
        // ** your code here **
   }
}
catch (OAuthException $e)
{
    // No token to be verified in the request, show a page where the user can enter the token to be verified
    // **your code here**
}

?>