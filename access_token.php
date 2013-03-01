<?php
/*
Filename: access_token.php
@author Jake Rego
2/21/2013
Description: Exchanges and authorized request for an access token.
			This access token and its associated 'secret' can be used
			in signing requests.
			
Source: code.google.com
*/


$server = new OAuthServer();
$server->accessToken();

?>