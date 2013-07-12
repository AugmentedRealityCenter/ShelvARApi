<?php
	include_once "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(stripos($oauth_user['scope'],"acctmod") === false) {
		exit(json_encode(array('result'=>"ERROR", 'errors'=> "No permission to modify account")));
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
	if(!count($err)) {
		$query = "UPDATE institutions SET ";
		$params = array();
		$type = "";
		
		$editName = false;
		$editAdmin = false;
		$editURL = false;
		$editAltContact = false;
		
		if(isset($_POST['inst_name']) && $_POST['inst_name'] != "") {
			$params[] = $_POST['inst_name'];
			$editName = true;
		}
		else if(isset($_GET['inst_name']) && $_GET['inst_name'] != "") {
			$params[] = $_GET['inst_name'];
			$editName = true;
		}
		
		if(isset($_POST['admin_contact']) && $_POST['admin_contact'] != "") {
			$params[] = $_POST['admin_contact'];
			$pending_email = $_POST['admin_contact'];
			$editAdmin = true;
		}
		else if(isset($_GET['admin_contact']) && $_GET['admin_contact'] != "") {
			$params[] = $_GET['admin_contact'];
			$pending_email = $_GET['admin_contact'];
			$editAdmin = true;
		}

		if(isset($_POST['inst_url']) && $_POST['inst_url'] != "") {
			$inst_url = $_POST['inst_url'];
			$editURL = true;
		}
		else if(isset($_GET['inst_url']) && $_GET['inst_url'] != "") {
			$inst_url = $_GET['inst_url'];
			$editURL = true;
		}
		
		if(isset($_POST['alt_contact']) && $_POST['alt_contact'] != "") {
			$alt_contact = $_POST['alt_contact'];
			$editAltContact = true;
		}
		else if(isset($_GET['alt_contact']) && $_GET['alt_contact'] != "") {
			$alt_contact = $_GET['alt_contact'];
			$editAltContact = true;
		}
		
		if($editName) {
			$query .= "name = ?,";
			$type .= "s";
		}
		if($editAdmin) {
			do {
				$activation_key = md5(uniqid(rand(), true));
				$activation_key = substr($activation_key, 0, 64);
		
				$db = new database();
				$db->query = "SELECT inst_id FROM institutions WHERE activation_key = ?";
				$db->params = array($activation_key);
				$db->type = 's';
		
				$result = $db->fetch();
			} while(!empty($result));
			$params[] = $activation_key;
			$params[] = 0;
			$query .= "pending_email = ?, activation_key = ?, email_verified = ?,";
			$type .= "ssi";
		}
		if($editURL) {
			$params[] = $inst_url;
			$query .= "inst_url = ?,";
			$type .= "s";
		}
		if($editAltContact) {
			$params[] = $alt_contact;
			$query .= "alt_contact = ?,";
			$type .= "s";
		}
		
		$query = substr($query,0,-1); // removing trailing comma
		$query .= " WHERE inst_id = ?";
		$type .= "s";
		$params[] = $inst_id;
		
		$db = new database();
		$db->query = "SELECT name, admin_contact FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';

		$result = $db->fetch();
		
		$previous_admin = $result[0]['admin_contact'];
		$previous_name = $result[0]['name'];

		$db = new database();
		$db->query = $query;
		$db->params = $params;
		$db->type = $type;
		
		if($db->update()) {
			if($editAdmin) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/api/institutions/send_activation_email.php");
			}
			if(!$err) {
				echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id, 'errors'=>"")); 
			}
		}
		else $err[] = "SQL Error";
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'inst_id'=>"", 'errors'=>$err)); 
	}
?>