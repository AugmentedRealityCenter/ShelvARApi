<?php
include_once "../api_ref_call.php";
include_once "../../database.php";
include_once "../../header_include.php";

$arr = array('user_id' => $oauth_user['user_id'],
	     'name'=> $oauth_user['name'],
	     'inst_id'=>$oauth_user['inst_id'],
	     'is_admin' => $oauth_user['is_admin'],
	     'email_verified' => $oauth_user['email_verified'],
	     'can_submit_inv' => $oauth_user['can_submit_inv'],
	     'can_read_inv' => $oauth_user['can_read_inv'],
	     'user_num' => $oauth_user['user_num'],
	     'is_superadmin' => $oauth_user['is_superadmin'],
	     'exp_date'=> $oauth_user['exp_date'],
	     'inst_name'=> $oauth_user['inst_name'],
	     'inst_activated'=> $oauth_user['inst_activated'],
		 'inst_email_activated' => $oauth_user['inst_email_activated'],
	     'inst_has_inv'=> $oauth_user['inst_has_inv'],
	     'result'=>'SUCCESS');
    print(json_encode($arr));

?>