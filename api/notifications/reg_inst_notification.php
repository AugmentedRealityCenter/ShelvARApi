<?php
	include "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	// need to check user permissions here
	$err = array();
	
	if (!isset($_GET['inst_id'])) {
		$err[] = 'Please fill in inst_id';
	}
	if (!isset($_GET['text'])) {
		$err[] = 'Please fill in the notification text';
	}
	
	// if no errors
	if (!count($err)) {
		$inst_id = $_GET['inst_id'];
		echo $inst_id."\n";
		$text = htmlspecialchars($_GET['text'], ENT_HTML401);
		echo $text."\n";
		$today = date('Y-m-d H:i:s');
		echo $today."\n";
		// TODO: insert this notification for all users who are admins of specified institution
		$db = new database();
		$db->query = "INSERT INTO notifications(text,read,create_time,user_id,inst_id)
						VALUES(?,?,?,?,?)";
		$db->params = array($text,0,$today,null,$inst_id);
		$db->type = 'sisss';
		
		if ($db->insert()) {
			if (!$err) {
				echo json_encode(array('result'=>"SUCCESS"));
			}
		} else {
			$err[] = "SQL error";
		}
	}
	
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'errors'=>$err));
	}
?>