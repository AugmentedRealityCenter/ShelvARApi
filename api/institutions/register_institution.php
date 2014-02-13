<?php 
	include "../../database.php";
	include_once "../../header_include.php";
	
	$err = array();
	
	if(!$_POST['inst_id']) {
		$err[] = 'Please fill in inst_id';
	}
	if(!$_POST['inst_name']) {
		$err[] = 'Please fill in inst_name';
	}
	if(!$_POST['admin_contact']) {
		$err[] = 'Please fill in admin_contact';
	}
	if(!$_POST['alt_contact']) {
		$err[] = 'Please fill in alt_contact';
	}
	if(!isset($_POST['inst_type'])) {
		$err[] = 'Please fill in inst_type';
	}
	if(!isset($_POST['inst_size'])) {
		$err[] = 'Please fill in inst_size';
	}
	if(strlen($_POST['inst_id'])<5 || strlen($_POST['inst_id'])>20) {
		$err[]='Institution ID must be between 5-20 characters';
	}
	if(preg_match('/[^a-z0-9\-\_\.]+/i',$_POST['inst_id'])) {
		$err[]='Institution ID contains invalid characters';
	}
	
	// If there are no errors
	if(!count($err)) {
		$inst_id = htmlspecialchars($_POST['inst_id'], ENT_HTML401);
		$name = htmlspecialchars($_POST['inst_name'], ENT_HTML401);
		$admin_contact = "";
		$inst_type = htmlspecialchars($_POST['inst_type'], ENT_HTML401);
		$inst_size = htmlspecialchars($_POST['inst_size'], ENT_HTML401);
		$is_activated = 0;
		$has_inv = 0;
		// set initial exp_date to 1 year after registering
		$today = date("Y-m-d H:i:s");
		$exp_date = strtotime(date("Y-m-d H:i:s", strtotime($today)) . "+1 year");
		$num_api_calls = 0;
		$alt_contact = htmlspecialchars($_POST['alt_contact'], ENT_HTML401);
		$inst_url = htmlspecialchars($_POST['inst_url'], ENT_HTML401);
		$pending_email = htmlspecialchars($_POST['admin_contact'], ENT_HTML401);
		$email_verified = 0;
		
		$db = new database();
		$db->query = "SELECT inst_id FROM institutions WHERE inst_id = ?";
		$db->params = array($inst_id);
		$db->type = 's';
		
		$result = $db->fetch(); 
		if(count($result) > 0) {  
			$err[]="Institution ID already taken";
		}
		else { 
			// generate email activation key
			do {
				$activation_key = md5(uniqid(rand(), true));
				$activation_key = substr($activation_key, 0, 64);
			
				$db = new database();
				$db->query = "SELECT inst_id FROM institutions WHERE activation_key = ?";
				$db->params = array($activation_key);
				$db->type = 's';
			
				$result = $db->fetch();
			} while(!empty($result));
			
			$db = new database();
			$db->query = "INSERT INTO institutions(inst_id,name,admin_contact,inst_type,inst_size,is_activated,has_inv,exp_date,num_api_calls,alt_contact,inst_url,pending_email,activation_key,email_verified)
							VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$db->params = array($inst_id, $name, $admin_contact, $inst_type, $inst_size, $is_activated, $has_inv, $exp_date, $num_api_calls, $alt_contact, $inst_url,$pending_email,$activation_key,$email_verified);
			$db->type = 'sssiiiisissssi';
			
			if($db->insert()) {
				include_once($_SERVER['DOCUMENT_ROOT'] . "/api/institutions/send_activation_email.php");
				if(!$err) {
					echo json_encode(array('result'=>"SUCCESS", 'inst_id'=>$inst_id, 'exp_date'=>$exp_date));
					$to = "kesanan@miamioh.edu";
					$subject = "New Shelvar Institution Registered";
					$message = "<img src='".$api."ShelvARLogo_Big.png' /><br/><br/>Dear ShelvAR Admin,<br/><br/>This email is to notify that a new institution has been registered on ShelvAR.<br/>
																										   <br/>Institution Name: ".$name.
																										   "<br/>Institution ID: ".$inst_id."<br/>";
																									
					$headers = 'From: ShelvAR.com <noreply@shelvar.com>' . "\r\n" .
							   'Reply-To: noreply@shelvar.com' . "\r\n" .
							   'Content-type: text/html' . "\r\n" .
							    'X-Mailer: PHP/' . phpversion();
					if(!mail($to, $subject, $message, $headers)) {
						$err[] = "Error sending confirmation email";
					}
				}
			}
			else {
				$err[] = "MySQL Error";
			}
		}
	}
	if($err) {
		echo json_encode(array('result'=>"ERROR", 'inst_id'=>"", 'errors'=>$err));
	}
?>