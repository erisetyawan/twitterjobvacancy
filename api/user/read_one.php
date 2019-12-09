<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../database.php';
include_once 'user.php';
include_once '../client.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$token = isset($_GET['token']) ? $_GET['token'] : error();

$client = new Client($db);
$client->token = $token;
$token_exists = $client->tokenExists();

if($token_exists){	 
	// prepare user object
	$user = new User($db);
	 
	// set ID property of record to read
	$user->id = isset($_GET['id']) ? $_GET['id'] : die();
	 
	// read the details of user to be edited
	$user->readOne();
	 
	if($user->name!=null){
		// create array
		$user_arr = array(
			"id" =>  $user->id,
			"name" => $user->name,
			"location" => $user->location,
		);
	 
		// set response code - 200 OK
		http_response_code(200);
	 
		// make it json format
		echo json_encode($user_arr);
	}
	 
	else{
		// set response code - 404 Not found
		http_response_code(404);
	 
		// tell the client user does not exist
		echo json_encode(array("message" => "User does not exist."));
	}
}
else{
	// set response code
	http_response_code(401);
 
	// tell the client access denied  & show error message
	echo json_encode(array("message" => "Token is not Registered."));
}

function error(){
	// set response code
	http_response_code(401);
 
	// tell the client access denied  & show error message
	echo json_encode(array("message" => "Access Denied."));
	die();
}
?>