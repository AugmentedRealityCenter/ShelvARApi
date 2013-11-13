<?php
	include "../../database.php";
	include_once "../../header_include.php";
	include_once "../api_ref_call.php";
	
	// need to check user permissions here
	
	$err = array();
	
	if (!$_POST['inst_id']) {
		$err[] = 'Please fill in inst_id';
	}
	if (!$_POST['text']) {
		$err[] = 'Please fill in the notification text';
	}
	
	// if no errors
	if (!count($err)) {
		$inst_id = $_POST['inst_id'];
		$text = htmlspecialchars($_POST['text'], ENT_HTML401);
		$today = date("Y-m-d H:i:s");
		// TODO: insert this notification for all users who are admins of specified institution
		$db = new database();
		$db->query = "INSERT INTO notifications(notif_id,text,read,create_time,user_id,inst_id)
						VALUES(?,?,?,?,?,?)";
		$db->params = array(NULL,$text,0,$today,NULL,$inst_id);
		$db->type = 'isisss';
		
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