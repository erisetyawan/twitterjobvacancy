<?php
	// required headers
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	 
	// include database and object files
	include_once 'database.php';
	include_once 'client.php';
	 
	// instantiate database and job object
	$database = new Database();
	$db = $database->getConnection();
	 
	// instantiate product object
	$client = new Client($db);
	 
	$firstname = isset($_GET['firstname']) ? $_GET['firstname'] : error('firstname');
	$lastname = isset($_GET['lastname']) ? $_GET['lastname'] : error('lastname');
	$email = isset($_GET['email']) ? $_GET['email'] : error('email');
	$password = isset($_GET['password']) ? $_GET['password'] : error('password');
	 
	// set product property values
	$client->firstname = $firstname;
	$client->lastname = $lastname;
	$client->email = $email;
	$client->password = $password;
	 
	// create the client
	if(
		!empty($client->firstname) &&
		!empty($client->email) &&
		!empty($client->password) &&
		!$client->emailExists()
	){
		$token = $client->create();
		// set response code
		http_response_code(200);
	 
		// display message: client was created
		echo json_encode(array("token" => $token, "message" => "Client was created."));
	}
	 
	// message if unable to create client
	else if($client->emailExists()){
		// set response code
		http_response_code(200);
	 
		// display message: unable to create client
		echo json_encode(array("token" => 0, "message" => "Email have been registered!"));
	}
	else{
	 
		// set response code
		http_response_code(400);
		
		// display message: unable to create client
		echo json_encode(array("message" => "Unable to create client."));
		
	}
	
	function error($error){
		// set response code
		http_response_code(400);
	 
		// tell error message
		echo json_encode(array("message" => $error." is required."));
		die();
	}
	
?>