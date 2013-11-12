<?php 
	include_once $_SERVER['DOCUMENT_ROOT'] . "/database.php";
	include_once $_SERVER['DOCUMENT_ROOT'] . "/header_include.php";
	
	echo "Creating notifications table...";
	$db = new mysqli($sql_server, $sql_user, $sql_password, $sql_database);
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
	$db->close();
?>