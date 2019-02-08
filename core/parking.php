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

		public function getVehicleId($plate, $userId=false){
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

		public function getVehicleActiveCategories($carId, $parking=""){
			global $conn;

			//Returns the categories the auto belongs in
			$query = $conn->query("SELECT CU.* FROM category_users AS CU INNER JOIN categories AS CT ON CU.category = CT.id WHERE CU.car = \"$carId\" AND CT.parking LIKE \"%$parking%\" AND CU.expiryDate < NOW() ");
			if($query){

				//categories
				$cats = array();

				if($query->num_rows){
					$cats = $query->fetch_all(MYSQLI_ASSOC);
				}

				return WEB::respond(true, "", $cats);
			}else{
				return WEB::respond(false, "Error: $conn->error");
			}
		}

		public function getVehicleChargingCategory($car, $parking){
			//returns a category to which we use to charge a car in certain parking
			global $conn;

			//check active categories

			$activeCategories = $this->getVehicleActiveCategories($car, $parking);

			if($activeCategories->status){
				//get categories
				$catsData = $activeCategories->data;

				if($catsData){
					//we choose with one to go with
					return WEB::respond(true, "", $catsData[0]);
				}else{
					//get parking's default category
					return $this->getDefaultCategory($parking);
				}
			}else{
				return $activeCategories;
			}
			die();

		}

		function carPlate($carId){
			global $conn;

			//Returns plate of the cars' id
			$query = $conn->query("SELECT plate FROM cars WHERE id = \"$carId\" ");
			if($query){
				if($query->num_rows){
					$data = $query->fetch_assoc();
					return WEB::respond(true, "", $data['plate']);
				}else{
					return WEB::respond(false, "Error: car does not exist");
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

		public function addCamera($cameraId, $function, $parking, $alias, $description, $userId){
			//associates camera to parking

			//check if the camera exists already
			global $conn;
			$query = $conn->query("SELECT * FROM parking_cameras WHERE camera = \"$cameraId\" AND parking = \"$parking\" ");
			if($query){
				if(!$query->num_rows){
					$q1 = $conn->query("INSERT INTO parking_cameras(camera, parking, alias, description, function, createdBy) VALUES(\"$cameraId\", \"$parking\", \"$alias\", \"$description\", \"$function\", \"$userId\") ");
					if($q1){
						return WEB::respond(true, "", array('id'=>$conn->insert_id));
					}else{
						return WEB::respond(false, "Error: $conn->error");
					}
				}else{
					return WEB::respond(false, "Error camera already exists");
				}
			}else{
				return WEB::respond(false, "Error: $conn->error");
			}
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

		public function getDefaultCategory($parkingId){
			//returns the default category
			$categories = $this->categories($parkingId);

			if($categories->status){
				$cats = $categories->data;
				//find the default category
				foreach ($cats as $key => $cat) {
					if($cat['isDefault']){
						return WEB::respond(true, "", $cat);
					}
				}

				//If nothing was returned then return error of no default category
				return WEB::respond(true, "No default category found");
			}else{
				return $categories;
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

			$query = $conn->query("SELECT * FROM category_users WHERE category = \"$categoryId\" AND archived = 'no' ");
			if($query){
				$data = $query->fetch_all(MYSQLI_ASSOC);
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
			$carData = $this->getVehicleId($plate, $userId);
			if($carData->status){

				$carId = $carData->data;

				//check if there is another active category
				$catsData = $this->getVehicleActiveCategories($carId);
				$vehicleCats = $catsData->data;

				if($catsData->status){

					if(!count($vehicleCats)){
						//vehicle does not belong in category

						$sql = "INSERT INTO category_users(car, expiryDate, category, createdBy) VALUES(\"$carId\", \"$expiryDate\", \"$categoryId\", \"$userId\")";
						$query = $conn->query($sql);
						if($query){
							return WEB::respond(true, 'Member added successfully');
						}else{
							return WEB::respond(false, "There was a database error $conn->error");
						}
					}else{
						return WEB::respond(false, "Error: vehicle belong in other categories");
					}
				}else{
					return $catsData;
				}
				
			}else{
				return WEB::respond(false, $carData->msg);
			}
		}

		public function addCategory($parkingId, $name, $description, $userId){
			//adds the parking category in the database
			global $conn;

			//check if there is a default category
			$defaultFlag = 0;
			$defaultCat = $this->getDefaultCategory($parkingId);
			if($defaultCat->status){
				//here we need ti add this as default
				$defaultFlag = 1;
			}

			$sql = "INSERT INTO categories(name, description, parking, isDefault, createdBy) VALUES(\"$name\", \"$description\", \"$parkingId\", \"$defaultFlag\", \"$userId\")";
			$query = $conn->query($sql);
			if($query){
				return WEB::respond(true, "", $conn->insert_id);
			}else{
				//query has failed
				return WEB::respond(false, "Error creating category $conn->error");
			}
		}

		public function getParkingFee($carId, $parking, $duration){
			//
			global $conn;

			//check the category a car belongs in
			$categories = $this->getVehicleChargingCategory($carId, $parking);
			if($categories->status){
				$category = $categories->data;
				// die();

				$categoryId = $category['id'];

				//Here we have to get the plans of this category
				$feePlans = $this->categoryFees($categoryId);

				if($feePlans->status){
					//Check the durations
					$feesData = $feePlans->data;
					$fee = 0;

					foreach ($feesData as $key => $plan) {
						$dur = (float)($plan['duration'].".000");
						if($dur >= "$duration"){
							$fee = $plan['fee'];
						}
					}

					return WEB::respond(true, '', $fee);
				}else{
					return $feePlans;
				}
			}else{
				return $categories;
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

		public function getCameras($parking, $function = ''){
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
	$Parking = new parking();
?>