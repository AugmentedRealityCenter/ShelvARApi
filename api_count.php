<?php 
$root = $_SERVER['DOCUMENT_ROOT']."/";
include_once $root."db_info.php";
include_once $root."database.php";
include_once $root."header_include.php";
include_once $root."api/api_ref_call.php";


// Wrapper class to increment the count for a specified API call

/**
 * This method determines if a given API call is under the count limit and can be used
 * returns true if the call is under its set limit
 * returns false if it is over the limit
 */
function is_incrementable($apiCall, $httpMethod, $oauth) {
	$user = $oauth['user_id'];
	checkLastReset($user);

	switch ($apiCall) {
		case "/book_pings/":
			if ($httpMethod = "GET") {
				// Get the number of calls made from the database
				$numCalls = getCountNotFreeCall("GET_book_pings_count", $user);
				$limit = grabLimit("GET book_pings");

				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			else {
				$numCalls = getCountNotFreeCall("POST_book_pings_count", $user);
				$limit = grabLimit("POST book_pings");

				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			break;
		case "book_pings/count":
			$numCalls = getCountNotFreeCall("GET_book_pings_count_count", $user);
			$limit = grabLimit("GET book_pings_count");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/book_pings/{book_ping_id}.json":
			$numCalls = getCountNotFreeCall("GET_book_pings_specific_count", $user);
			$limit = grabLimit("GET book_pings_specific");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/book_tags/{book_tag}.json":
			$numCalls = getCountFreeCall("GET_book_tags_count");
			$limit = grabLimit("GET book_tags");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/lc_numbers/{call_number}.json":
			$numCalls = getCountFreeCall("GET_lc_numbers_count");
			$limit = grabLimit("GET lc_numbers");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/institutions/":
			if ($httpMethod = "GET") {
				$numCalls = getCountFreeCall("GET_insitutions_count");
				$limit = grabLimit("GET institutions");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			else {
				$numCalls = getCountFreeCall("POST_institutions_count");
				$limit = grabLimit("POST institutions");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			break;
		case "/institutions/edit":
			$numCalls = getCountFreeCall("POST_institutions_edit_count");
			$limit = grabLimit("POST institutions_edit");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/institutions/{inst_id}.json":
			$numCalls = getCountFreeCall("GET_institutions_specific_count");
			$limit = grabLimit("GET institutions_specific");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/institutions/available/{inst_id}.json":
			$numCalls = getCountFreeCall("GET_institutions_available_count");
			$limit = grabLimit("GET institutions_available");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/users":
			if ($httpMethod = "GET") {
				$numCalls = getCountFreeCall("GET_users_count");
				$limit = grabLimit("GET users");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			else {
				$numCalls = getCountFreeCall("POST_users_count");
				$limit = grabLimit("POST users");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			break;
		case "/users/edit":
			$numCalls = getCountFreeCall("POST_users_edit_count");
			$limit = grabLimit("POST users_edit");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/users/{user_id}/permissions":
			if ($httpMethod = "GET") {
				$numCalls = getCountFreeCall("GET_users_permissions_count");
				$limit = grabLimit("GET users_permissions");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			else {
				$numCalls = getCountFreeCall("POST_users_persmissions_count");
				$limit = grabLimit("POST users_permissions");
					
				if ($numCalls < $limit)
					return true;
				else
					return false;
			}
			break;
		case "/users/{user_id}.json":
			$numCalls = getCountFreeCall("GET_users_specific_count");
			$limit = grabLimit("GET users_specific");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/users/available/{user_id}.json":
			$numCalls = getCountFreeCall("GET_users_available_count");
			$limit = grabLimit("GET users_available");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/make_tags/{paper_type}.pdf":
			$numCalls = getCountFreeCall("GET_make_tags_count");
			$limit = grabLimit("GET make_tags");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case " /make_tags/paper_formats":
			$numCalls = getCountFreeCall("GET_paper_formats_count");
			$limit = grabLimit("GET paper_formats");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/oauth/get_request_token":
			$numCalls = getCountFreeCall("GET_oauth_request_token_count");
			$limit = grabLimit("GET oauth_request_token");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/oauth/login":
			$numCalls = getCountFreeCall("GET_oauth_login_count");
			$limit = grabLimit("GET oauth_login");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/oauth/get_access_token":
			$numCalls = getCountFreeCall("GET_oauth_access_token_count");
			$limit = grabLimit("GET oauth_access_token");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
		case "/oauth/whoami":
			$numCalls = getCountFreeCall("GET_oauth_whoami_count");
			$limit = grabLimit("GET oauth_whoami");

			if ($numCalls < $limit)
				return true;
			else
				return false;
			break;
				
	}
}

/**
 * Increments a given API call by a provided count
 */
function increment_count($apiCall, $httpMethod, $count, $oauth) {
	$user = $oauth['user_id'];
	
	switch ($apiCall) {
		case "/book_pings/":
			if ($httpMethod = "GET") {
				updateCountNotFreeCall("GET_book_pings_count", $count, $user);
			}
			else {
				updateCountNotFreeCall("POST_book_pings_count", $count, $user);
			}
			break;
		case "/book_pings/count":
			updateCountNotFreeCall("GET_book_pings_count_count", $count, $user);
			break;
		case "/book_pings/{book_ping_id}.json":
			updateCountNotFreeCall("GET_book_pings_specific_count", $count, $user);
			break;
		case "/book_tags/{book_tag}.json":
			updateCountFreeCall("GET_book_tags_count", $count);
			break;
		case "/lc_numbers/{call_number}.json":
			updateCountFreeCall("GET_lc_numbers_count", $count);
			break;
		case "/institutions/":
			if ($httpMethod = "GET") {
				updateCountFreeCall("GET_insitutions_count", $count);
			}
			else {
				updateCountFreeCall("POST_institutions_count", $count);
			}
			break;
		case "/institutions/edit":
			updateCountFreeCall("POST_institutions_edit_count", $count);
			break;
		case "/institutions/{inst_id}.json":
			updateCountFreeCall("GET_institutions_specific_count", $count);
			break;
		case "/institutions/available/{inst_id}.json":
			updateCountFreeCall("GET_institutions_available_count", $count);
			break;
		case "/users":
			if ($httpMethod = "GET") {
				updateCountFreeCall("GET_users_count", $count);
			}
			else {
				updateCountFreeCall("POST_users_count", $count);
			}
			break;
		case "/users/edit":
			updateCountFreeCall("POST_users_edit_count", $count);
			break;
		case "/users/{user_id}/permissions":
			if ($httpMethod = "GET") {
				updateCountFreeCall("GET_users_permissions_count", $count);
			}
			else {
				updateCountFreeCall("POST_users_persmissions_count", $count);
			}
			break;
		case "/users/{user_id}.json":
			updateCountFreeCall("GET_users_specific_count", $count);
			break;
		case "/users/available/{user_id}.json":
			updateCountFreeCall("GET_users_available_count", $count);
			break;
		case "/make_tags/{paper_type}.pdf":
			updateCountFreeCall("GET_make_tags_count", $count);
			break;
		case " /make_tags/paper_formats":
			updateCountFreeCall("GET_paper_formats_count", $count);
			break;
		case "/oauth/get_request_token":
			updateCountFreeCall("GET_oauth_request_token_count", $count);
			break;
		case "/oauth/login":
			updateCountFreeCall("GET_oauth_login_count", $count);
			break;
		case "/oauth/get_access_token":
			updateCountFreeCall("GET_oauth_access_token_count", $count);
			break;
		case "/oauth/whoami":
			updateCountFreeCall("GET_oauth_whoami_count", $count);
			break;
	}
}

/**
 * Checks to see when the counters where last reset in the user table
 * and the unknown user table. If there hasn't been a reset in over 15 mins
 * the counts are set to zero.
 */
function checkLastReset($user) {
	$lastResetNotFree = grabLastResetNotFree($user);
	$lastResetFree = grabLastResetFree();
	
	$currTime = time();  // http://www.php.net/manual/en/function.time.php
	$fifteenMins = 900;
	
	if (($currTime - $lastResetNotFree) > $fifteenMins) {
		setAllNotFreeCountsToZero($user);
		setNotFreeLastReset($user);
	}
	if (($currTime - $lastResetFree) > $fifteenMins) {
		setAllFreeCountsToZero();
		setFreeLastReset();
	}
}

/**
 * Grabs the last reset field from the users table
 */
function grabLastResetNotFree($user) {
	$query = "SELECT last_reset " .
			"FROM users ".
			"WHERE user_id = ?";
	$params = array($user);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	
	$results = $db->fetch();
	return strtotime( implode($results[0]) );
}

/**
 * Grabs the last reset field from the unknown users table
 */
function grabLastResetFree() {
	handleIPAddress();
	$query = "SELECT last_reset " .
			"FROM unknown_users ".
			"WHERE ip_address = ?";
	$ip_address = $_SERVER["REMOTE_ADDR"]; // Gets user IP address ??
	$params = array($ip_address);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	
	$results = $db->fetch();
	return strtotime( implode($results[0]) );
}

function handleIPAddress() {
	$query = "SELECT * " .
			"FROM unknown_users ". 
			"WHERE ip_address = ?";
	$ip_address = $_SERVER["REMOTE_ADDR"];
	$params = array($ip_address);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	
	$results = $db->fetch();
	
	if ($results == NULL || count($results) == 0) {
		$query = "INSERT INTO unknown_users(ip_address) " .
			"VALUES (?)" ;
		$params = array($ip_address);
		$type = "s";
	
		$db2 = new database();
		$db2->query = $query;
		$db2->params = $params;
		$db2->type = $type;
		$db2->insert();
	}
}

/**
 * Sets each Non-free API call counter to zero
 */
function setAllNotFreeCountsToZero($user) {
	$NotFreeCalls = array("POST_book_pings_count", "GET_book_pings_count", "GET_book_pings_count_count",
			"GET_book_pings_specific_count");
	
	foreach($NotFreeCalls as $count) {
		setToZeroNotFreeHelper($count, $user);
	}
}

/**
 * Sets each free API call counter to zero
 */
function setAllFreeCountsToZero() {
	$freeCalls = array("GET_book_tags_count", "GET_lc_numbers_count", "POST_institutions_count",
			"GET_insitutions_count", "POST_institutions_edit_count", "GET_institutions_specific_count",
			"GET_institutions_available_count", "GET_users_count", "POST_users_count",
			"POST_users_edit_count", "GET_users_permissions_count", "POST_users_persmissions_count",
			"GET_users_specific_count", "GET_users_available_count", "GET_make_tags_count",
			"GET_paper_formats_count", "GET_oauth_request_token_count", "GET_oauth_login_count",
			"GET_oauth_access_token_count", "GET_oauth_whoami_count");
	
	foreach($freeCalls as $count) {
		setToZeroFreeHelper($count);
	}
}

/**
 * Helper method to  set a column to zero
 */
function setToZeroFreeHelper($column) {
	$query = "UPDATE unknown_users " .
			"SET " . $column . " = 0 " .
			"WHERE ip_address = ?";
	$ip_address = $_SERVER["REMOTE_ADDR"]; // Gets user IP address ??
	$params = array($ip_address);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

/**
 * Helper method to  set a column to zero
 */
function setToZeroNotFreeHelper($column, $user) {
	$query = "UPDATE users " .
			"SET " . $column . " = 0 " .
			"WHERE user_id = ?";
	$params = array($user);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

/**
 * Sets the last reset field to the current time in the
 * users table
 */
function setNotFreeLastReset($user) {
	$query = "UPDATE users " .
			"SET last_reset = ? ".
			"WHERE user_id = ?";
	$time = date("Y-m-d H:i:s", strtotime("now"));
	$params = array($time, $user);
	$type = "ss";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

/**
 * Sets the last reset field to the current time in the
 * unknown users table
 */
function setFreeLastReset() {
	$query = "UPDATE unknown_users " .
			"SET last_reset = ? ".
			"WHERE ip_address = ?";
	$time = date("Y-m-d H:i:s", strtotime("now"));
	$ip_address = $_SERVER["REMOTE_ADDR"]; // Gets user IP address ??
	$params = array($time, $ip_address);
	$type = "ss";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

/**
 * Gets the number of calls for a particular API call from 
 * the database.
 * @param unknown_type $column  The count column for the intended call
 * @return unknown $numCalls    The number of calls for that API (within 15 min window)
 */
 function getCountNotFreeCall($column, $user) {
	$queryNumCalls = "SELECT " . $column . " " .
			"FROM users ".
			"WHERE user_id = ?";
	$paramsNumCalls = array($user);
	$typeNumCalls = "s";
	
	$db = new database();
	$db->query = $queryNumCalls;
	$db->params = $paramsNumCalls;
	$db->type = $typeNumCalls;
	
	$results = $db->fetch();
	$numCalls = intval( $results[0] );
	
	return $numCalls;
}

function getCountFreeCall($column) {
	$queryNumCalls = "SELECT " . $column . " " .
	"FROM unknown_users ".
	"WHERE ip_address = ?";
	$ip_address = $_SERVER["REMOTE_ADDR"];
	$paramsNumCalls = array($ip_address);
	$typeNumCalls = "s";
	
	$db = new database();
	$db->query = $queryNumCalls;
	$db->params = $paramsNumCalls;
	$db->type = $typeNumCalls;
	
	$results = $db->fetch();
	$numCalls = intval( $results[0] );
	
	return $numCalls;
}

/**
 * This method increments the count for the given column in the Users 
 * table (where the paid API calls are kept track of)
 * @param unknown_type $column  Name of the column to be incremented
 */
function updateCountNotFreeCall ($column, $count, $user) {
	$query = "UPDATE users " .
			"SET " . $column . " = (" . $column . " + " . $count . ") " .
			"WHERE user_id = ?";
	$params = array($user);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

function updateCountFreeCall($column, $count) {
	$query = "UPDATE unkown_users " .
			"SET " . $column . " = " . $column . " + " . $count . " " .
			"WHERE ip_address = ?";
	$ip_address = $_SERVER["REMOTE_ADDR"];
	$params = array($ip_address);
	$type = "s";
	
	$db = new database();
	$db->query = $query;
	$db->params = $params;
	$db->type = $type;
	$db->update();
}

/**
 * Gets the limit set for an API call from the json file.
 * @param unknown_type $call  The API call being made
 * @return number limit  The limit set on that call
 */
function grabLimit($call) {
	$tempValues = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/ratelimits.json');
	$json_arr = json_decode($tempValues);
	
	foreach($json_arr as $options){
		if($options->api_call === $call){
			return intval( $options->limit );
		}
	}
	
	return 0;
}

?>