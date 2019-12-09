<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../database.php';
 
// instantiate job object
include_once 'job.php';
 
$database = new Database();
$db = $database->getConnection();
 
$job = new Job($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
if(
    !empty($data->id) &&
	!empty($data->id_user) &&
    !empty($data->text) &&
	!empty($data->created) &&
){
 
    // set job property values
    $job->id = $data->id;
	$job->id_user = $data->id_user;
    $job->text = $data->text;
    $job->created = date('Y-m-d H:i:s');
 
    // create the job
    if($job->create()){
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "Job was created."));
    }
 
    // if unable to create the job, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Unable to create job."));
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to create job. Data is incomplete."));
}
?>