<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once 'database.php';
include_once 'job.php';
include_once 'client.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();

$token = isset($_GET['token']) ? $_GET['token'] : error();

$client = new Client($db);
$client->token = $token;
$token_exists = $client->tokenExists();

if($token_exists){	 
	// prepare job object
	$job = new Job($db);
	 
	// set ID property of record to read
	$job->id = isset($_GET['id']) ? $_GET['id'] : die();
	 
	// read the details of job to be edited
	$job->readOne();
	 
	if($job->name!=null){
		// create array
		$job_arr = array(
			"id" =>  $job->id,
			"name" => $job->name,
			"location" => $job->location,
			"text" => html_entity_decode($job->text),
		);
	 
		// set response code - 200 OK
		http_response_code(200);
	 
		// make it json format
		echo json_encode($job_arr);
	}
	 
	else{
		// set response code - 404 Not found
		http_response_code(404);
	 
		// tell the client job does not exist
		echo json_encode(array("message" => "Job does not exist."));
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