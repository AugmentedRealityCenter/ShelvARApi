<?php
/**
Filename: oauth_register.php
@author Jake Rego
2/21/2013
Description: Registers a new user, updates information from the
			 completed form, and gets the complete consumer credentials
			 from the database.
			 
* Note: When you want to update a previously registered consumer,
		then supply the id of the consumer, the consumer_key and the
		consumer_secret. The key and secret can not be changed and 
		are used as extra verification during the update. 

Source: code.google.com
*/

// need include files login.php?

// The currently logged on user
$user_id = 1;

// This should come from a form filled in by the requesting user
$consumer = array(
    // These two are required
    'requester_name' => 'John Doe',
    'requester_email' => 'john@example.com',

    // These are all optional
    'callback_uri' => 'http://www.myconsumersite.com/oauth_callback',
    'application_uri' => 'http://www.myconsumersite.com/',
    'application_title' => 'John Doe\'s consumer site',
    'application_descr' => 'Make nice graphs of all your data',
    'application_notes' => 'Bladibla',
    'application_type' => 'website',
    'application_commercial' => 0
);

// Register the consumer
$store = OAuthStore::instance(); 
$key   = $store->updateConsumer($consumer, $user_id);

// Get the complete consumer from the store
$consumer = $store->getConsumer($key);

// Some interesting fields, the user will need the key and secret
$consumer_id = $consumer['id'];
$consumer_key = $consumer['consumer_key'];
$consumer_secret = $consumer['consumer_secret'];


?>