<?php	
if(!isset($_GET['oauth_token'])) {
  echo "No token supplied";
  exit;
 }
	
$err = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['login'])) {
  if(!$_POST['user_id']) {
    $err[] = 'No username supplied';	
    exit;
  }
  if(!count($err)) {
    $user_id = $_POST['user_id'];
    $user_id = strtolower($user_id);
    $password = $_POST['password'];

    include_once("../db_info.php");
    include_once("../database.php");

    /******************* Prepared Statement ******************************/
    $db = new database();
    $db->query = "SELECT user_id, inst_id, password, encrip_salt, user_num
						  FROM users
						  WHERE user_id = ?";
    $db->params = array($user_id);
    $db->type = 's';
    /********************************************************************/
		
    $result = $db->fetch();
			
    // If there is a username that matches
    if(count($result) > 0) {
      $salt = $result[0]['encrip_salt'];
      // Hash the password
      $check_password = hash('sha256', trim($password) . $salt );

      if($check_password != $result[0]['password']) { 
	echo 'Incorrect password';
	exit;
      } 
    }
    else {
      echo 'No record of username';
      exit;
    }

    $db = new database();
    $db->query = "SELECT exp_date FROM institutions WHERE inst_id = ?";
    $inst_id = $result[0]['inst_id'];
    $db->params = array($inst_id);
    $db->type = 's';
    $res2 = $db->fetch();

    session_start();
    $_SESSION['user_num'] = $result[0]['user_num'];

    echo("<html><head><meta http-equiv=\"refresh\" content=\"0;post_login?oauth_token=" . $_GET['oauth_token'] . "\"></head></html>");
    exit(200);
  }
 }

echo("<html><body>"
     ."<img src=\"../ShelvARLogo_Big.png\" /><br/>"
     ."Please log in your ShelvAR account."
     ."<form method='POST' action='?oauth_token=" .  
     $_GET['oauth_token'] . "'>" .
       "Username <input name='user_id' type='input'><br />
        Password <input name='password' type='password'><br />
	<input name='login' type='submit' value='Log in'>
      </form></body></html>");

?>
