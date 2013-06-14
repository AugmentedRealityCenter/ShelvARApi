<?php 
	include "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(!$_POST['inst_id'] || !$_POST['name'] || !$_POST['admin_contact'] || !$_POST['alt_contact'] || !$_POST['inst_type'] || !$_POST['inst_size']) {
		$err[] = 'Please fill in all fields';
	}
	if(strlen($_POST['inst_id'])<5 || strlen($_POST['inst_id'])>20) {
		$err[]='Institution ID must be between 5-20 characters';
	}
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['inst_id'])) {
		$err[]='Institution ID contains invalid characters';
	}
	
	// If there are no errors
	if(!count($err)) {
		$inst_id = $_POST['inst_id'];
		$name = $_POST['inst_name'];
		$admin_contact = "";
		$inst_type = $_POST['inst_type'];
		$inst_size = $_POST['inst_size'];
		$is_activated = 0;
		$has_inv = 0;
		// set initial exp_date to 1 month after registering
		$today = date("Y-m-d H:i:s");
		$exp_date = strtotime(date("Y-m-d H:i:s", strtotime($today)) . "+1 month");
		$num_api_calls = 0;
		$alt_contact = $_POST['alt_contact'];
		$inst_url = $_POST['inst_url'];
		$pending_email = $_POST['admin_contact'];
		$email_verified = 0;
		
		$db = new database();
		$db->query = "SELECT inst_id FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch(); 
		if(count($result) > 0) {  
			$err[]="Institution ID already taken";
		}
		else { 
			// generate email activation key
			do {
				$activation_key = md5(uniqid(rand(), true));
				$activation_key = substr($activation_key, 0, 64);
			
				$db = new database();
				$db->query = "SELECT inst_id FROM institutions WHERE activation_key = ?";
				$db->params = array($activation_key);
				$db->type = 's';
			
				$result = $db->fetch();
			} while(!empty($result));
			
			$db = new database();
			$db->query = "INSERT INTO institutions(inst_id,name,admin_contact,inst_type,inst_size,is_activated,has_inv,exp_date,num_api_calls,alt_contact,inst_url,pending_email,activation_key,email_verified)
							VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$db->params = array($inst_id, $name, $admin_contact, $inst_type, $inst_size, $is_activated, $has_inv, $exp_date, $num_api_calls, $alt_contact, $inst_url,$pending_email,$activation_key,$email_verified);
			$db->type = 'sssiiiisissssi';
			
			if($db->insert()) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/api/institutions/send_activation_email.php");
				if(!$err) {
					echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id));
				}
			}
			else {
				$err[] = "MySQL Error";
			}
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'inst_id'=>"", 'errors'=>$err));
	}
?>