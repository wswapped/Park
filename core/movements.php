<?php
	class movement extends parking{
		public function add($name, $username, $phone, $email, $profile_picture = '', $gender='')
		{
			# adds system user
			global $conn;
			$query = $conn->query("INSERT INTO users(names, username, phone, email, profile_picture, gender) VALUES (\"$name\", \"$username\", \"$phone\", \"$email\", \"$profile_picture\", \"$gender\") ") or trigger_error("Error  $conn->error");
			return $conn->insert_id;
		}

		public function parkList($parkingId)
		{
			# returns movement in list of parkingId
			global $conn;
			if(is_array($parkingId)){
				$parkingQ = implode($parkingId, ", ");
				//select entry
				$sql = "SELECT * FROM movement WHERE parking IN ($parkingQ) AND type = 'entry' ORDER BY movement.time DESC ";
				$query = $conn->query($sql) or trigger_error("Error $conn->error");
				$movement = array();
				while ($data = $query->fetch_assoc()) {
					//check the corresponding exit
					$car = $data['car'];
					$entryTime = $data['time'];

					$sql = "SELECT * FROM movement WHERE parking IN($parkingQ) AND type = 'exit' AND time>\"$entryTime\" LIMIT 1";
					$exiQ = $conn->query($sql) or trigger_error($conn->error);
					$data['exitMovement'] = '';
					$data['duration'] = '';
					$data['fees'] = '';
					if($exiQ->num_rows){
						$exitMovement = $exiQ->fetch_assoc();

						$exitTime = $exitMovement['time'];

						$interval = strtotime($exitTime) - strtotime($entryTime);

						$minutes = $interval/(3600);

						$fees = $this->getParkingFee($car, $data['parking'], $minutes);
						if($fees->status){
							$data['fees'] = $fees->data;
						}

						$data['exitMovement'] = $exitMovement;
					}
					$movement[] = $data;
				}
				return $movement;
			}else{
				return false;
			}
		}

		public function types($userId){
			//finds the types of the user
			global $conn;
			$types = array();

			#!this searching function not select in user
			$details = $this->details($userId);

			$q = $conn->query("SELECT role FROM system_roles WHERE user = \"$userId\" AND archived = 'no' ") or trigger_error($conn->error);
			if($q){
				while ($data = $q->fetch_assoc() ) {
					$types = array_merge($types, array($data['role']));
				};
			}

			return $types;
		}

		public function lastMovement($plate, $type = '')
		{
			//returns the last movement of the car
			global $conn;

			$query = $conn->query("SELECT * FROM movement WHERE car = \"$plate\" AND type LIKE \"%$type%\" ");
			if($query){
				//check is there is record
				if($query->num_rows){
					$data = $query->fetch_assoc();

					return WEB::respond(true, "", $data);
				}else{
					return WEB::respond(false, "No movement found");
				}
			}else{
				//maybe there was an error
				return WEB::respond(false, "Error $conn->error");
			}
		}

		public function getTypeUsers($type){
			//finds the types of the user
			global $conn;

			$users = array();

			//check admins
			if($type == 'admin'){
				$query = $conn->query("SELECT id FROM users WHERE account_type = 'admin' ") or trigger_error("Error $conn->error");
				while ($data = $query->fetch_assoc()) {
					$users = array_merge($users, array($data['id']));
				}

			}else if($type == 'supplier'){
				WEB::loadClass('supplier');
				global $Supplier;

				$allSuppliers = $Supplier->list();
				foreach ($allSuppliers as $key => $supplier) {
					$users = array_merge($users, array($supplier['supplierId']));
				}
			}else{
				//user not recognized so far
			}
			
			return $users;
		}

		public function updatePassword($userId, $password)
		{
			# TODO: make secure
			global $conn;


			$query = $conn->query("UPDATE users SET password  = \"$password\" WHERE id = \"$userId\"") or trigger_error("Error $conn->error");

			if($query)
				return true;
			else
				return false;
		}
		public function updateProfilePicture($userId, $profile_picture)
		{
			# TODO: make secure
			global $conn;


			$query = $conn->query("UPDATE users SET profile_picture  = \"$profile_picture\" WHERE id = \"$userId\"") or trigger_error("Error $conn->error");

			if($query)
				return true;
			else
				return false;
		}
	}

	$Movement = new movement();
?>