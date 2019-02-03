<?php
ob_start();

//specify JSON as content type
header('Content-Type: application/json');

include_once '../../core/conn.php';
include_once '../../core/web.php';
include_once '../../core/user.php';
include_once '../../core/parking.php';
include_once '../../core/camera.php';
include_once '../../core/movements.php';

define('DB_DATE_FORMAT', 'Y-m-d H:i:s');

$standard_date = "Y-m-d h:i:s";

//gather all requests together
$request = array_merge($_GET, $_POST);

//cleaning all the post variables against attacks
foreach ($request as $key => $value) {
	// $request[$key] = $conn->real_escape_string($value);
}

//getting requested action
$action = $request['action']??"";

//return wrapper
$response = array();
if($action == 'carEntry'){
	//MArk entry of the car
	$carPlate = $request['carPlate']??"";
	$parkId = $request['parkId']??"";
	$cameraId = $request['cameraId']??"";

	//check if the car has entered in less than a minute ago
	$lastMovement = $Movement->lastMovement($carPlate);
	$interval = 0;
	if($lastMovement->status){
		$movement = $lastMovement->data;

		$time = strtotime($movement['time']);
		$interval = (time() - $time)/60;

		if(abs($interval) < 1){
			//here user need to wait for a minute
			$timeConstraint = 1;
		}
	}else{
		//Has never been here
		$timeConstraint = 3;
		$interval = 0;
	}

	//check time constraint
	if(!empty($timeConstraint) && $timeConstraint){
		//marking the entry of a car
		$query = $conn->query("INSERT INTO `movement` (`car`, `type`, `parking`, `camera`, `time`) VALUES (\"$carPlate\", 'entry', \"$parkId\", \"$cameraId\", CURRENT_TIMESTAMP)");
		if($query){
			//check if the user has some money
			$response = form_response(true);
		}else{
			$response = form_response(false, 'DB error '.$conn->error);
		}
	}else{
		$response = form_response(false, 'You moved very recently, just '.abs($interval));
	}	
}else if($action == 'carExit'){
	//Mark the exit of the car
	$carPlate = $request['carPlate']??"";
	$parkId = $request['parkId']??"";
	$cameraId = $request['cameraId']??"";

	$fees = 0;

	//check when the car has entered
	$lastMovement = $Movement->lastMovement($carPlate);
	if($lastMovement->status){
		$movement = $lastMovement->data;

		$time = strtotime($movement['time']);
		$interval = (time() - $time)/60;

		//start the payment
		if($interval < 15){
			//
			$fees = 0;
		}else if($interval <= 60){
			$fees = 200;
		}elseif($interval <= 120){
			$fees = 300;
		}else{
			$fees = 400;
		}
	}

	//marking the exit of a car
	$query = $conn->query("INSERT INTO `movement` (`car`, `type`, `parking`, `camera`, `time`) VALUES (\"$carPlate\", 'exit', \"$parkId\", \"$cameraId\", CURRENT_TIMESTAMP)");
	if($query){
		//check if the user has some money
		$response = form_response(true, "", array('fees'=>$fees));
	}else{
		$response = form_response(false, 'DB error '.$conn->error);
	}
}else if($action == 'lastMovement'){
	//returns the last movement of the car
	$plate = $request['plate'];
	$type = $request['type']??""; //type of preferred movement

	$cooperative = $request['cooperative']??"";

	$name = $request['name']??"";
	$phone = $request['phone']??"";
	$NID = $request['NID']??"";
	$gender = $request['gender']??"";
	$location = $request['location']??"";
	$birth_date = date("Y-m-d", strtotime($request['birth_date']))??false;
	$date = $request['date']??date($standard_date);
	// $date = $request['']??date($standard_date);

	if(!empty($_FILES['picture'])){
		$pic = $_FILES['picture'];
		$ext = strtolower(pathinfo($pic['name'], PATHINFO_EXTENSION)); //extension
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg')
		{
			$filename = "images/farmer/$name".time().".$ext";
			if(move_uploaded_file($pic['tmp_name'], "../$filename")){

			}else{
				//set the default image
				$filename = "images/farmer/default.jpg";

			}
		}
	}else{
		$filename = "images/farmer/default.jpg";
	}
	

	// checking if essential details ares et
	if(!($name && $NID && $gender)){
		$response = array('status'=>false, 'msg'=>"Provide all member details");		
	}else{

		//insert the user in DB
		$userId = $Cooperative->add_user($name , $phone , $NID , $gender, $birth_date, $filename);

		if($userId){
			add_farmer_cooperative($userId, $cooperative);
			$response = array('status'=>true, 'data'=>array('memberId'=>$userId));
		}else{
			$response = array('status'=>false, 'msg'=>"$conn->error");
		}
	}
}else if($action == 'addUser'){
	//Adding user to the system
	$name = $request['name']??"";
	$email = $request['email']??"";
	$role = $request['role']??"";
	$parking = $request['parking']??"";
	$phone = $request['phone']??"";
	$gender = $request['gender']??"";
	$password = $request['password']??"";

	//check if all parameters were set
	if($name && (($email && (filter_var($email, FILTER_VALIDATE_EMAIL)) || ($phone && WEB::validatePhone($phone))) && $role && $parking && $gender && $password)){
		//create a user in system
		$userCreation = $User->add($name, $phone, $email, '', $gender);
		if($userCreation->status){
			//creation was successful
			$userId = $userCreation->data['id'];
			
			//Let's add system role
			$systemId = $User->attachRole($userId, $role);

			if($systemId->status){
				//adding to parking
				$roleAddition = $Parking->addRole($systemId->data['id'], $parking);
				if($roleAddition->status){
					$response = WEB::respond(true, '', array('id'=>$userId));
				}else
					$response = $roleAddition;
				
			}else{
				//response from systemId
				$response = $systemId;
			}

			

			
		}else{
			//error creating user is returned
			$response = $userCreation;
		}
	}else{
		//somethng was wrong
		$response = WEB::response(false, 'Form was not filled well. Please check if all fields are filled and with correct values');
	}
}else if($action == 'addParkingCategory'){
	//Adding parking category
	$name = $request['name']??"";
	$description = $request['description']??"";
	$parking = $request['parking']??"";
	$userId = $request['userId']??"";

	$fees = $request['fees'];

	//check if all parking category essentials were set
	if($name && $description && $parking && $userId){
		//here we can add the category
		$catStatus = $Parking->addCategory($parking, $name, $description, $userId);
		if($catStatus->status){
			//category created successfully
			//lets check if the fees were also specified
			if(!empty($fees)){
				$categoryId = $catStatus->data;
				
				//lets add money
				foreach ($fees as $duration => $fee) {
					$Parking->addCategoryFee($categoryId, $duration, $fee, $userId);
				}
			}
			$response = WEB::respond(true);
		}else{
			$response = WEB::respond(false, $catStatus->msg);
		}
	}else{
		//somethng was wrong
		$response = WEB::respond(false, 'Form was not filled well. Please check if all fields are filled and with correct values');
	}
}else if($action == 'addParkingCamera'){
	//Adding parking camera
	$usage = $request['usage']??"";
	$description = $request['description']??"";
	$address = $request['address']??"";
	$alias = $request['alias']??"";
	$parking = $request['parking']??"";
	$userId = $request['userId']??"";

	//check if all camera essentials were set
	if($usage && $description && $address && $alias && $parking && $userId){

		//add camera
		$cameraAdd = $Camera->add($address, $userId);

		if($cameraAdd->status){
			//Associate the camera with parking
			$cameraId = $cameraAdd->data['id'];
			$associate = $Parking->addCamera($cameraId, $usage, $parking, $alias, $description, $userId);

			$response = $associate;

		}else{
			$response = $cameraAdd;
		}
	}else{
		//somethng was wrong
		$response = WEB::respond(false, 'Form was not filled well. Please check if all fields are filled and with correct values');
	}
}else if(){
	getCamera
}else if($action == 'addCategoryMember'){
	//Adding car member to category
	$plate = $request['plate']??"";
	$expiryDate = $request['expiryDate']??"";
	$category = $request['category']??"";
	$userId = $request['userId']??"";

	//check if all form essentials were set
	if($plate && $expiryDate && $category && $userId){

		//formating expiryDate
		$expiryDate = date(DB_DATE_FORMAT, strtotime($expiryDate));

		$catStatus = $Parking->addCategoryMembers($plate, $expiryDate, $category, $userId);
		if($catStatus->status){
			//member added successfully
			$response = WEB::respond(true);
		}else{
			$response = WEB::respond(false, $catStatus->msg);
		}
	}else{
		//somethng was wrong
		$response = WEB::respond(false, 'Form was not filled well. Please check if all fields are filled and with correct values');
	}
}
else{
	$response = array('status'=>false, 'msg'=>"Specifiy action");
}

echo json_encode($response);

//Utility functions
function form_response($status, $msg='', $data= array()){
	// header("Content-Type: application/html");
	//removing nulls from response dataa
	$data = checknull($data);
	return array('status'=>$status, 'msg'=>$msg, 'data'=>$data);
}

function checknull($array){
	$cool_array  = array();
	//checks array again null

	// $depth = array_depth($array);

	// for($n=0; $n<$depth; $n++){
	// 	foreach ($variable as $key => $value) {
	// 		# code...
	// 	}
	// }

	// if($depth == 1){
	// }

	// if(is_array($value)){
	// 	cool_array[$key] = checknull($value);
	// }else{
	// 	$cool_array[$key] = $array[$key]??"";
	// }

	return $array;
}
function array_depth(array $array) {
	$max_depth = 1;

	foreach ($array as $value) {
		if (is_array($value)) {
			$depth = array_depth($value) + 1;

			if ($depth > $max_depth) {
				$max_depth = $depth;
			}
		}
	}

	return $max_depth;
}
?>