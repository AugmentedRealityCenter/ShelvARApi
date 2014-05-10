<?php
	include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
	include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";
	                                  
	if(isset($_GET['inst_id'])) {                                                               
		$inst_id = $_GET['inst_id'];                                  
		$db = new database();
		$db->query = "SELECT inst_id FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch();
		if(!empty($result)) {
			echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>"TAKEN"));
		}
		else echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>"AVAILABLE"));
	}
	else echo json_encode(array('result'=>"ERROR", 'message'=>"No institution ID supplied"));
?>
