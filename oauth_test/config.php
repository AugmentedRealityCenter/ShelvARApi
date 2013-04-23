<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
//$consumerKey 	= 'aaaa8eb070955e55476ea103bc74f664ae0d540b';
//$consumerSecret = '26ae0d26573f4f26aed5053b3d967128241e367a';

$consumerKey =  '4cecfad7d0857d14cce8fc42307e3ba6ad7e0160';
$consumerSecret = 'afe2b073bec637904f0414d2e4d69f03c213ad93';


// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '319f499ec8090e92e5ca0d188cc663';
$tokenSecret	= '025b6bd5c6';

// Endpoints, at least change the urls to where you left the endpoint scripts
//$apiURL	 	= 'http://oauth.freek/oauth/src/example/provider/api.php';
$accessURL	 	= 'http://devapi.shelvar.com/oauth/access_token.php';
$requestURL 	= 'http://devapi.shelvar.com/oauth/request_token.php';
$authorizeURL   = 'http://devapi.shelvar.com/oauth/login.php';
$callbackURL    = 'http://devapi.shelvar.com/oauth_test/get_access_token.php';

?>