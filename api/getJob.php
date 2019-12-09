<?php
include_once 'database.php';
include_once 'job/job.php';
include_once 'user/user.php';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/1.1/search/tweets.json?q=%23loker%20OR%20%23lowongankerja%20OR%20%23infoloker&src=typed_query&&count=500');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$headers = array();
$headers[] = 'Authorization: Bearer AAAAAAAAAAAAAAAAAAAAAMKLAgEAAAAA2XHkyQtczbgGc9K3sYpvIZvbNWQ%3DgeIMfFIaBepr5OrU4zrgfvDNIVonnOPr2eDD6MfI4Avth094ow';
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$statuses = json_decode($result)->statuses;
//print_r($statuses);

$database = new Database();
$db = $database->getConnection();

$i = 0;
while($i < count($statuses)){
	if(substr($statuses[$i]->text,0,4)=='RT @'){
		//hanya retweet
	}
	else{
		$user = new User($db);

		// get posted data
		$id_user = $statuses[$i]->user->id_str;
		$name = $statuses[$i]->user->screen_name;
		$location = $statuses[$i]->user->location;

		// make sure data is not empty
		if(
			!empty($id_user) &&
			!empty($name) &&
			!empty($location)
		){	 
			// set user property values
			$user->id = $id_user;
			$user->name = $name;
			$user->location = $location;

			// create the user
			if($user->create()){
				// tell the user
				echo json_encode(array("message" => "User ".$name." was created."."<br>"));
			}

			// if unable to create the user, tell the user
			else{
				// tell the user
				echo json_encode(array("message" => "Unable to create ".$name."."."<br>"));
			}
		}	 
		// tell the user data is incomplete
		else{
			// tell the user
			echo json_encode(array("message" => "Unable to create ".$name.". Data is incomplete."."<br>"));
		}

		/////

		$job = new Job($db);

		// get posted data
		$id_job = $statuses[$i]->id_str;
		$text = $statuses[$i]->text;
		$created = $statuses[$i]->created_at;

		// make sure data is not empty
		if(
			!empty($id_job) &&
			!empty($text) &&
			!empty($created)
		){

			// set job property values
			$job->id = $id_job;
			$job->id_user = $id_user;
			$job->text = $text;
			$job->created = convertDate($created);

			// create the job
			if($job->create()){
				// tell the user
				echo json_encode(array("message" => "Job ".$id_job." was created."."<br>"));
			} 
			// if unable to create the job, tell the user
			else{
				// tell the user
				echo json_encode(array("message" => "Unable to create ".$id_job."."."<br>"));
			}
		}

		// tell the user data is incomplete
		else{
			// tell the user
			echo json_encode(array("message" => "Unable to create ".$id_job.". Data is incomplete."."<br>"));
		}
	}	
	$i++;
}

function convertDate($created){
	$arr = explode(" ",$created);
	
	$tgl = $arr[2];
	$bln = convertMonth($arr[1]);
	$thn = $arr[5];
	return $thn."-".$bln."-".$tgl;
}

function convertMonth($month){
	if($month == 'Jan') return '01'; 
	else if($month == 'Feb') return '02'; 
	else if($month == 'Mar') return '03'; 
	else if($month == 'Apr') return '04'; 
	else if($month == 'May') return '05'; 
	else if($month == 'Jun') return '06'; 
	else if($month == 'Jul') return '07'; 
	else if($month == 'Aug') return '08'; 
	else if($month == 'Sep') return '09'; 
	else if($month == 'Oct') return '10'; 
	else if($month == 'Nov') return '11'; 
	else return '12';
}

?>