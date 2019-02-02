<?php
	class camera{
		public function add($address, $userId)
		{
			# adds system user
			global $conn;
			$query = $conn->query("INSERT INTO cameras(address,createdBy) VALUES (\"$address\", \"$userId\") ");
			if($query){
				return WEB::respond(true, "", array('id'=>$conn->insert_id));
			}else{
				return WEB::respond(false, "Error adding new camera: $conn->error");
			}
		}



		public function details($camera){
			//returns the cameras in the parking
			global $conn;

			$query = $conn->query("SELECT * FROM cameras WHERE id = \"$camera\"");
			if($query){
				if($query->num_rows){
					$cameras = $query->fetch_assoc();
					return WEB::respond(true, "", $cameras);
				}else{
					return WEB::respond(true, "", "Camera does not exist");
				}

				

			}else{
				return WEB::respond(false, "Error $conn->error");
			}
		}

		public function getCameras($parking, $function = 'entry'){
			//returns the cameras in the parking
			global $conn;

			$query = $conn->query("SELECT * FROM parking_cameras WHERE parking = \"$parking\" AND function LIKE \"%$function%\" ");
			if($query){
				$cameras = array();

				if($query->num_rows){
					$cameras = $query->fetch_all(MYSQLI_ASSOC);
				}

				return WEB::respond(true, "", $cameras);

			}else{
				return WEB::respond(false, "Error $conn->error");
			}
		}
	}
	$Camera = new camera();
?>