<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
//$consumerKey 	= 'aaaa8eb070955e55476ea103bc74f664ae0d540b';
//$consumerSecret = '26ae0d26573f4f26aed5053b3d967128241e367a';

$consumerKey = 'ff74c3b3ca577275f5499a6e43f52aa62831f1d1';
$consumerSecret = '91c749240d2d98e46df4b0d3547c5e28dee013bf';

// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '319f499ec8090e92e5ca0d188cc663';
$tokenSecret	= '025b6bd5c6';

// Endpoints, at least change the urls to where you left the endpoint scripts
//$apiURL	 	= 'http://oauth.freek/oauth/src/example/provider/api.php';
$accessURL	 	= 'http://devapi.shelvar.com/oauth/access_token.php';
$requestURL 	= 'http://devapi.shelvar.com/oauth/request_token.php';
$authorizeURL   = 'http://devapi.shelvar.com/oauth/login.php';
//$callbackURL	= 'oob';
$callbackURL    = 'http://devapi.shelvar.com/oauth_test/get_access_token.php';

?>