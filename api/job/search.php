<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../core.php';
include_once '../database.php';
include_once 'job.php';
include_once '../client.php';
 
// instantiate database and job object
$database = new Database();
$db = $database->getConnection();

$token = isset($_GET['token']) ? $_GET['token'] : error();

$client = new Client($db);
$client->token = $token;
$token_exists = $client->tokenExists();

if($token_exists){	 

	// initialize object
	$job = new Job($db);
	 
	// get keywords
	$keywords=isset($_GET["s"]) ? $_GET["s"] : "";
	 
	// query jobs
	$stmt = $job->search($keywords);
	$num = $stmt->rowCount();
	 
	// check if more than 0 record found
	if($num>0){
	 
		// jobs array
		$jobs_arr=array();
		$jobs_arr["records"]=array();
	 
		// retrieve our table contents
		// fetch() is faster than fetchAll()
		// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
			// extract row
			// this will make $row['name'] to
			// just $name only
			extract($row);
	 
			$job_item=array(
				"id" => $id,
				"name" => $name,
				"location" => $location,
				"text" => html_entity_decode($text),
				"created" => $created,
			);
	 
			array_push($jobs_arr["records"], $job_item);
		}
	 
		// set response code - 200 OK
		http_response_code(200);
	 
		// show jobs data
		echo json_encode($jobs_arr);
	}
	 
	else{
		// set response code - 404 Not found
		http_response_code(404);
	 
		// tell the client no jobs found
		echo json_encode(
			array("message" => "No jobs found.")
		);
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