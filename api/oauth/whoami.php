<?php
include_once "../api_ref_call.php";
include_once "../../database.php";
include_once "../../header_include.php";

try {
  $user_num = $Provider->getUserId();
  $db = new database();
  $db->query = "SELECT * FROM users WHERE user_num = ?";
  $db->params = array($user_num);
  $db->type = "i";
  $the_rec = $db->fetch();

  if(count($the_rec) > 0){
    $arr = array('user_id' => $the_rec[0]['user_id'], 'inst_id'=>$the_rec[0]['inst_id'],'result'=>'SUCCESS');
    print(json_encode($arr));
  } else {
    $arr = array('user_id'=>"", 'inst_id'=>"", 'result' => "ERROR No user id found.");
    print(json_encode($arr));
  }

} catch (Exception $Exception) {
  exit(json_encode(array("result"=>"ERROR. OAuth token missing or invalid.")));
}

?>