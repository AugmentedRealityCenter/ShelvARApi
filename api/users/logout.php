<?php	
	session_start();
	$_SESSION = array();
    session_destroy();
	header("Location: http://dev.shelvar.com/loginTest/");
?>