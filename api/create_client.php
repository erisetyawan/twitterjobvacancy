<?php
	// required headers
	header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	 
	// files needed to connect to database
	include_once 'database.php';
	include_once 'client.php';
	 
	// get database connection
	$database = new Database();
	$db = $database->getConnection();
	 
	// instantiate product object
	$client = new Client($db);
	 
	// get posted data
	$data = json_decode(file_get_contents("php://input"));
	 
	// set product property values
	$client->firstname = $data->firstname;
	$client->lastname = $data->lastname;
	$client->email = $data->email;
	$client->password = $data->password;
	 
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
?>