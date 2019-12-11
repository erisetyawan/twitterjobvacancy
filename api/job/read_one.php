<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
// include database and object files
include_once '../database.php';
include_once 'job.php';
include_once '../client.php';
 
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
		$text = html_entity_decode($job->text);
		
		$pos = strrpos($text, "https://t.co/");
		$i = $pos;

		while(true){
			if($i==strlen($text)) break;
			$char = substr($text,$i,1);
			if ( ctype_space($char) ) break;
			$i++;
		}
		$link = substr($text,$pos,$i);
		$temp = explode("#",$link);
		$link = $temp[0];
		$temp = explode(" ",$link);
		$link = $temp[0];
		
		/////////////////////////////////////////

		$pos2 = strpos($text, "https://t.co/");
		$i2 = $pos2;

		while(true){
			if($i2==strlen($text)) break;
			$char = substr($text,$i2,1);
			if ( ctype_space($char) ) break;
			$i2++;
		}
		$link2 = substr($text,$pos2,$i2);
		$temp = explode("#",$link2);
		$link2 = $temp[0];
		$temp = explode(" ",$link2);
		$link2 = $temp[0];
		
		/////////////////////////////////////////

		if($link == $link2)$link2 = "";
		if($i!=strlen($text))$link2 = $link;

		$text = str_replace($link,"",$text);
		$text = str_replace($link2,"",$text);
		
		$link = "https://twitter.com/i/web/status/".$job->id;
		
		// create array
		$job_arr = array(
			"id" =>  $job->id,
			"name" => $job->name,
			"location" => $job->location,
			"text" => html_entity_decode($text),
			"link_twitter" =>  $link,
			"other_link" => $link2,
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