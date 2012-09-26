<?php
$result = array('result' => "ERROR Could not find valid licensing information for your site. If this was in error, please contact the site administrator with the following information. IP: ".$_SERVER['REMOTE_ADDR']);
echo json_encode($result);
?>
