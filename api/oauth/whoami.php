<?php
include_once $_SERVER['DOCUMENT_ROOT']."/api/api_ref_call.php";
include_once $_SERVER['DOCUMENT_ROOT']."/database.php";
include_once $_SERVER['DOCUMENT_ROOT']."/header_include.php";

$oauth_user = get_oauth();

$arr = array('user_id' => $oauth_user['user_id'],
	     'name'=> $oauth_user['name'],
	     'inst_id'=>$oauth_user['inst_id'],
	     'is_admin' => $oauth_user['is_admin'],
	     'email_verified' => $oauth_user['email_verified'],
	     'can_submit_inv' => $oauth_user['can_submit_inv'],
	     'can_read_inv' => $oauth_user['can_read_inv'],
		 'can_shelf_read' => $oauth_user['can_shelf_read'],
	     'user_num' => $oauth_user['user_num'],
	     'is_superadmin' => $oauth_user['is_superadmin'],
	     'exp_date'=> $oauth_user['exp_date'],
	     'inst_name'=> $oauth_user['inst_name'],
	     'inst_activated'=> $oauth_user['inst_activated'],
		 'inst_email_activated' => $oauth_user['inst_email_activated'],
	     'inst_has_inv'=> $oauth_user['inst_has_inv'],
		 'scope' => $oauth_user['scope'],
	     'result'=>'SUCCESS');
    print(json_encode($arr));

?>
