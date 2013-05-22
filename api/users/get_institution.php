<?php
	include '../../database.php';
	include_once "../../header_include.php";
	//include '../../connect.php';
	include_once "../api_ref_call.php";
	
	//private $dbuser_username = new database();
	$db = new database();
	
	$array = array();
	$inst_id = $_GET['inst_id'];
	
	$db->query = "SELECT * FROM institution WHERE inst_id = ?";
	$db->params = array($inst_id);

	$db->type = 's';
	$the_rec = $db->fetch();
	
	if(count($the_rec)>0){
		unset($the_rec[0]['inst_num']);
		unset($the_rec[0]['is_activated']);
		$arr = array('login' => $the_rec, 'result'=>"SUCCESS");
		print json_encode($arr);
	} else {
		$arr = array('login' => "", 'result'=>"ERROR");
		print json_encode($arr);
	}
	
	//$dbuser_username = mysql_real_escape_string($_POST['name']);
	//$dbuser_username = $database->__get('name');

	
	//$dbuser_password = mysql_real_escape_string($_POST['password']);
	//$dbuser_password = mysql_query("SELECT FROM users WHERE password=" + $_POST['password']);
	
	
	//$_GET['email']) = mysql_real_escape_string($_POST['email']);
	//$_GET['inst_id']) = mysql_real_escape_string($_POST['inst_id']);


	//$_GET['description']) = mysql_real_escape_string($_POST['description']); //varchar
	//$_GET['inst_size']) = mysql_real_escape_string($_POST['inst_size']); //enum
?>