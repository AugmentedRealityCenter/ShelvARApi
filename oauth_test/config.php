<?php
/**
 * Config file used to 'setup' the basic example with your own data.
 *
 * @author      Freek Lijten <freek@procurios.nl>
 */

// Consumer key and secret. Change these into the key and secret you generated
//$consumerKey 	= 'aaaa8eb070955e55476ea103bc74f664ae0d540b';
//$consumerSecret = '26ae0d26573f4f26aed5053b3d967128241e367a';

//$consumerKey =  'ff74c3b3ca577275f5499a6e43f52aa62831f1d1';
//$consumerSecret = '72488eb8a6b5c6eebe16a380583ad8dc804e7ee1';

$consumerKey = '771916368aba1bcb693a576033bb3ac56298ca2c';
$consumerSecret = '59fc35e7c2fb4d130de2d18f9c38928a34ffbcd9';

// Access token and secret. Change these into the ones you received at the end of the OAuth handshake cycle
$token			= '319f499ec8090e92e5ca0d188cc663';
$tokenSecret	= '025b6bd5c6';

// Endpoints, at least change the urls to where you left the endpoint scripts
$apiURL	 	= 'http://devapi.shelvar.com/book_pings/count';
$accessURL	 	= 'http://devapi.shelvar.com/oauth/access_token.php';
$requestURL 	= 'http://devapi.shelvar.com/oauth/request_token.php';
$authorizeURL   = 'http://devapi.shelvar.com/oauth/login.php';
$callbackURL    = 'http://devapi.shelvar.com/oauth_test/get_access_token.php';

?>