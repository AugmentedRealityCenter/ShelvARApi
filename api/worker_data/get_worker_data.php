<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/api/api_ref_call.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/database.php';

$oauth_user = get_oauth();
$inst_id    = $oauth_user['inst_id'];
$user_id    = $oauth_user['user_id'];

echo json_encode($oauth_user);
?>
