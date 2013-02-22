<?php
	$dbhost = "localhost";   
	$dbname = "shelvar"; 	
	$dbuser = "";  
	$dbpass = "";
	
	mysql_connect($dbhost, $dbuser, $dbpass) or die("MySQL Error: " . mysql_error());  
	mysql_select_db($dbname) or die("MySQL Error: " . mysql_error());
?>