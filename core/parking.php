<?php
	class parking{
		public function add($name, $username, $phone, $email, $profile_picture = '', $gender='')
		{
			# adds system user
			global $conn;
			$query = $conn->query("INSERT INTO users(names, username, phone, email, profile_picture, gender) VALUES (\"$name\", \"$username\", \"$phone\", \"$email\", \"$profile_picture\", \"$gender\") ") or trigger_error("Error  $conn->error");
			return $conn->insert_id;
		}

		public function addRole($systemRole, $parkingId){
			//addds system role to a parking
			global $conn;
			//check if user is already there
			$q = $conn->query("SELECT * FROM parking_roles WHERE systemRole = \"$systemRole\" AND parking = \"$parkingId\" AND archived = 'no'");
			if($q){
				//check if there is rows returned
				if($q->num_rows){
					return WEB::respond(false, "Parking role already exists and is active");
				}else{
					//here we can add role now
					$query = $conn->query("INSERT INTO parking_roles(systemRole, parking) VALUES(\"$systemRole\", \"$parkingId\")");
					if($query)
						return WEB::respond(true, '', array('id'=>$conn->insert_id));
					else
						return WEB::respond(false, "Error adding role $conn->error");
				}
			}else{
				return WEB::respond(false, "Error checking role existence $conn->error");
			}
		}

		public function details($id)
		{
			# returns parking details
			global $conn;
			$query = $conn->query("SELECT * FROM parking WHERE id = \"$id\" LIMIT 1 ") or trigger_error("Error $conn->error");
			$data = $query->fetch_assoc();
			$data['capacity'] = $this->totalCapacity($id);
			return $data;
		}

		public function totalCapacity($id)
		{
			# returns total capacity of the parking
			global $conn;
			$query = $conn->query("SELECT SUM(capacity) as capacity FROM parking_zones WHERE parking = \"$id\" LIMIT 1 ") or trigger_error("Error $conn->error");
			$data = $query->fetch_assoc();
			return $data['capacity']??0;
		}

		public function userList($userId){
			//finds user's parkings
			global $conn;
			$sql = "SELECT P.*, SR.role, RN.printName FROM parking AS P JOIN parking_roles AS PR ON PR.parking = P.id JOIN system_roles as SR ON PR.systemRole = SR.id JOIN role_names AS RN ON RN.name = SR.role WHERE SR.user = \"$userId\" AND PR.archived = 'no' ";
			$q = $conn->query($sql) or trigger_error($conn->error);
			if($q){
				return $q->fetch_all(MYSQLI_ASSOC);
			}else{
				return false;
			}
		}

		public function getCarId($plate, $userId=false){
			global $conn;
			//returns the id of the car and create it
			$userId = (int)$userId;

			//check if it is their
			$query = $conn->query("SELECT * FROM cars WHERE plate = \"$plate\" ");
			if($query){
				if($query->num_rows){
					//car exist
					$data = $query->fetch_assoc();
					return WEB::respond(true, "", $data['id']);
				}else{
					//here we insert the car
					$iquery = $conn->query("INSERT INTO cars(plate, createdBy) VALUES(\"$plate\", $userId) ");
					if($iquery){
						// return the id
						return WEB::respond(true, "", $conn->insert_id);
					}else{
						return WEB::respond(false, "Error inserting car: $conn->error");
					}
				}
			}else{
				return WEB::respond(false, "Error: $conn->error");
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

		public function categories($parkingId)
		{
			# List of categories in the parking
			global $conn;

			$query = $conn->query("SELECT * FROM categories where parking = \"$parkingId\" ");
			if($query){
				$data = $query->fetch_all(MYSQLI_ASSOC);

				return WEB::respond(true, '', $data);
			}else{
				return WEB::respond(false, "There was a database error $conn->error");
			}
		}

		public function getCategory($categoryId)
		{
			# Details on the category
			global $conn;

			$query = $conn->query("SELECT * FROM categories where id = \"$categoryId\" AND archived = 'no' ");
			if($query){
				$data = $query->fetch_assoc(); 

				//get the fees
				$fees = $this->categoryFees($categoryId);
				$data['fees'] = $fees;
				return WEB::respond(true, '', $data);
			}else{
				return WEB::respond(false, "There was a database error $conn->error");
			}
		}

		public function getCategoryMembers($categoryId)
		{
			# Details on the category
			global $conn;

			$query = $conn->query("SELECT * FROM categories WHERE id = \"$categoryId\" AND archived = 'no' ");
			if($query){
				$data = $query->fetch_assoc();
				return WEB::respond(true, '', $data);
			}else{
				return WEB::respond(false, "There was a database error $conn->error");
			}
		}

		public function addCategoryMembers($plate, $expiryDate, $categoryId, $userId)
		{
			# Add member to the category
			global $conn;

			//CHECK IF the car is available
			$carData = $this->getCarId($plate, $userId);
			if($carData->status){
				$carId = $carData->data;

				$sql = "INSERT INTO category_users(car, expiryDate, category, createdBy) VALUES(\"$carId\", \"$expiryDate\", \"$categoryId\", \"$userId\")";
				$query = $conn->query($sql);
				if($query){
					return WEB::respond(true, 'Member added successfully');
				}else{
					return WEB::respond(false, "There was a database error $conn->error");
				}
			}else{
				return WEB::respond(false, $carData->msg);
			}

			
		}

		public function addCategory($parkingId, $name, $description, $userId){
			//adds the parking category in the database
			global $conn;
			$sql = "INSERT INTO categories(name, description, parking, createdBy) VALUES(\"$name\", \"$description\", \"$parkingId\", \"$userId\")";
			$query = $conn->query($sql);
			if($query){
				return WEB::respond(true, "", $conn->insert_id);
			}else{
				//query has failed
				return WEB::respond(false, "Error creating category $conn->error");
			}
		}

		public function categoryFees($categoryId){
			//returns the duration fee
			global $conn;

			$query = $conn->query("SELECT * FROM category_fees WHERE category = \"$categoryId\" ");
			if($query){
				return WEB::respond(true, '', $query->fetch_all(MYSQLI_ASSOC));
			}else{
				return WEB::respond(false, "Error: $conn->error");
			}
		}

		public function addCategoryFee($categoryId, $duration, $fee, $userId){
			//adds the parking category in the database
			global $conn;
			$sql = "INSERT INTO category_fees(category, duration, fee, createdBy) VALUES(\"$categoryId\", \"$duration\", \"$fee\", \"$userId\")";
			$query = $conn->query($sql);
			if($query){
				return WEB::respond(true, $conn->insert_id);
			}else{
				//query has failed
				return WEB::respond(false, "Error adding category duration fee $conn->error");
			}
		}
	}
	$Parking = new parking();
?>