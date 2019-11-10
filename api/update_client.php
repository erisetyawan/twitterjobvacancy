<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/client.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate client object
$client = new Client($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt=isset($data->jwt) ? $data->jwt : "";
 
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show client details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        // set client property values
		$client->firstname = $data->firstname;
		$client->lastname = $data->lastname;
		$client->email = $data->email;
		$client->password = $data->password;
		$client->id = $decoded->data->id;
		 
		// update the client record
		if($client->update()){
			// we need to re-generate jwt because client details might be different
			$token = array(
			   "iss" => $iss,
			   "aud" => $aud,
			   "iat" => $iat,
			   "nbf" => $nbf,
			   "data" => array(
				   "id" => $client->id,
				   "firstname" => $client->firstname,
				   "lastname" => $client->lastname,
				   "email" => $client->email
			   )
			);
			$jwt = JWT::encode($token, $key);
			 
			// set response code
			http_response_code(200);
			 
			// response in json format
			echo json_encode(
					array(
						"message" => "Client was updated.",
						"jwt" => $jwt
					)
				);
		}
		 
		// message if unable to update client
		else{
			// set response code
			http_response_code(401);
		 
			// show error message
			echo json_encode(array("message" => "Unable to update client."));
		}
    }
 
    // if decode fails, it means jwt is invalid
	catch (Exception $e){
	 
		// set response code
		http_response_code(401);
	 
		// show error message
		echo json_encode(array(
			"message" => "Access denied.",
			"error" => $e->getMessage()
		));
	}
}
 
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the client access denied
    echo json_encode(array("message" => "Access denied."));
}
?>