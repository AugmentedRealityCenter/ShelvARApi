<?php
/**
Filename: check_oauth_request.php
@author Jake Rego
2/20/2013
Description: At the start of every request handled by your application,
			you can check if the request contains OAuth authorization information.
Source: code.google.com
*/
function checkRequest() {
	if (OAuthRequestVerifier::requestIsSigned())
	{
			try
			{
					$req = new OAuthRequestVerifier();
					$user_id = $req->verify();

					// If we have an user_id, then login as that user (for this request)
					if ($user_id)
					{
							// **** Add your own code here ****
					}
			}
			catch (OAuthException $e)
			{
					// The request was signed, but failed verification
					header('HTTP/1.1 401 Unauthorized');
					header('WWW-Authenticate: OAuth realm=""');
					header('Content-Type: text/plain; charset=utf8');
											
					echo $e->getMessage();
					exit();
			}
	}
}

?>