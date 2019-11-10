<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once 'database.php';
include_once 'job.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare job object
$job = new Job($db);
 
// get id of job to be edited
$data = json_decode(file_get_contents("php://input"));
 
// set ID property of job to be edited
$job->id = $data->id;
 
// set job property values
$job->title = $data->title;
$job->description = $data->description;
 
// update the job
if($job->update()){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(array("message" => "Job was updated."));
}
 
// if unable to update the job, tell the user
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(array("message" => "Unable to update job."));
}
?>