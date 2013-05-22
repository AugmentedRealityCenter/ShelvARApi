<?php
include_once "../api_ref_call.php";
include_once "../../database.php";
include_once "../../header_include.php";

$arr = array('user_id' => $oauth_user['user_id'], 'inst_id'=>$oauth_user['inst_id'], 'is_admin' => $oauth_user['is_admin'], 'can_submit_inv' => $oauth_user['can_submit_inv'], 'can_read_inv' => $oauth_user['can_read_inv'], 'is_superadmin' => '0', 'exp_date'=> $oauth_user['exp_date'], 'result'=>'SUCCESS');
    print(json_encode($arr));

?>