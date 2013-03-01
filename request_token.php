<?php
/*
Filename: request_token.php
@author Jake Rego
2/21/2012
Description: Once the consumer has a 'key' and 'secret', a request is made
			for a request token in order to obtain user authorization.
Source: code.google.com
*/

$server = new OAuthServer();
$token = $server->requestToken();
exit();


?>