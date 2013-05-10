<?php
include_once "../api_ref_call.php";
include_once "../../database.php";
include_once "../../header_include.php";

$arr = array('user_id' => $user['user_id'], 'inst_id'=>$user['inst_id'], 'is_admin' => $user['is_admin'], 'can_submit_data' => $user['can_submit_data'], 'can_read_data' => $user['can_read_data'], 'is_superadmin' => '0', 'result'=>'SUCCESS');
    print(json_encode($arr));

?>