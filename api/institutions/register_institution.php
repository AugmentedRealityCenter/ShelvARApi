<?php 
	include "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	$err = array();
	
	if(!$_POST['inst_id'] || !$_POST['description'] || !$_POST['admin_contact'] || !$_POST['alt_contact'] || !$_POST['inst_type'] || !$_POST['inst_size']) {
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
		$description = $_POST['description'];
		$admin_contact = $_POST['admin_contact'];
		$alt_contact = $_POST['alt_contact'];
		$inst_type = $_POST['inst_type'];
		$inst_size = $_POST['inst_size'];
		
		$db = new database();
		$db->query = "SELECT * FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch(); 
		if(count($result) > 0) {  
			$err[]="Institution ID already taken";
		}
		else { 
			$db = new database();
			$db->query = "INSERT INTO institutions(inst_id,description,admin_contact,alt_contact,inst_type,inst_size,is_activated,exp_date,num_api_calls)
							VALUES(?,?,?,?,?,?,?,?,?)";
			$db->params = array($inst_id, $description, $admin_contact, $alt_contact, $inst_type, $inst_size, 0, date('Y-m-d H:i:s'), 0);
			$db->type = 'ssssssisi';
			
			if($db->insert()) {
				echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id));
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