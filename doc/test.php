<?php 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";
	
	$db = new database();
	$db->query = "SELECT exp_date FROM institutions WHERE inst_id = ?";
	$db->params = array("miamioh");
	$db->type = 's';
	
	$result = $db->fetch();
	
	echo json_encode($result);
	/*if ($db->connect_errno) {
		echo "Connect failed: ".$db->connect_error;
		exit();
	}
	$query = "CREATE TABLE IF NOT EXISTS `notifications` (".
				"`notif_id` int(11) NOT NULL AUTO_INCREMENT,".
				"`text` varchar(240) NOT NULL,".
				"`read` binary(1) NOT NULL,".
				"`create_time` datetime NOT NULL,".
				"`user_id` varchar(40) DEFAULT NULL,".
				"`inst_id` varchar(40) NOT NULL,".
				"PRIMARY KEY (`notif_id`)".
				");";
	if ($db->query($query) === TRUE) {
		echo "done!";
	} else {
		echo "\nError ".$db->errno.": ".$db->error;
	}
	$db->close();*/
?>