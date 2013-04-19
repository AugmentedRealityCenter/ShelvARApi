<?php

/**
 * @file
 * Submit a list of "book pings" to the database. Data should be provided by POST, and conform with the
 * requirements listed in book_ping_lib.php.
 *
 * @sa do_book_ping
 *
 * Copyright 2011 by ShelvAR Team. 
 * 
 * @version Sept 13, 2011
 * @author Bo Brinkman
 */

include_once "book_ping_lib.php";
include_once "../../header_include.php";
//include_once "../api_ref_call.php";
/** @cond */
$ret; //!< return value from function call that does most of the work
$ret = do_book_ping(stripslashes($_POST["book_pings"]),"miamioh");
Print json_encode(array('result'=>$ret)); 
//var_dump($_POST);
//var_dump($_GET);
/** @endcond */  
?>
