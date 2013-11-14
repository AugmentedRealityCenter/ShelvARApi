<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(stripos($oauth_user['scope'],"acctmod") === false) {
		exit(json_encode(array('result'=>'ERROR No permission to access account.')));
	}
	
	$inst_id = "";
	if(!$_POST['inst_id']) {
		if(!$_GET['inst_id']) {
			$err[] = "No inst_id supplied";
		}
		else $inst_id = $_GET['inst_id'];
	}
	else $inst_id = $_POST['inst_id'];
	
	if($inst_id != $oauth_user['inst_id'] || $oauth_user['is_superadmin'] == 0) {
		$err[] = "Invalid access to institution account";
	}

	// if all preliminary checks passed
	if (!count($err)) {
		$cond = false;
		$limSet = false;
		$query = "SELECT * FROM notifications ";
		$qArray = array();
		$paramsList = array();
		$types = "";
		
		if(isset($_GET["start_date"])){
			$qArray[] = "create_time >= ?";
			$paramsList[] = urldecode($_GET["start_date"]);
			$cond = true;
			$types .= "s";
		}
		if(isset($_GET["end_date"])){
			$qArray[] = "create_time < ?";
			$paramsList[] = urldecode($_GET["end_date"]);
			$cond = true;
			$types .= "s";
		}
		if(isset($_GET["num_limit"]) && (is_int($_GET["num_limit"]) || ctype_digit($_GET["num_limit"]))){
			$paramsList[] = $_GET["num_limit"];
			$cond = true;
			$limSet = true;
			$types .= "s";
		}

		if (!$cond) {
			$query = $query . " LIMIT 0,20";
			
			$db = new database();
			$db->query = $query;
			$db->params = $paramsList;
			$db->type = "";
		}
		else {
			$query = $query . " WHERE ";
			
			$query .= implode(" AND ", $qArray);
			if ($limSet)
				$query = $query . " LIMIT 0,?";
			else 
				$query = $query . " LIMIT 0,20";
			
			$db = new database();
			$db->query = $query;
			$db->params = $paramsList;
			$db->type = $types;
		}
		$result = $db->fetch();
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err)); 
	}
?>