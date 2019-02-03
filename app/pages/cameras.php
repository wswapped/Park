<?php
	//check if we are viewing specific category
	$categoryId = $_GET['cid']??"";

	//TODO: Check if the user is allowed to access this category
	if(!empty($categoryId)){
		include_once "viewCategory.php";
	}else{
		//uSERS of the category
		$catUsers = $Parking->getCategoryMembers($categoryId);

		$catUsersData = $catUsers->data;
		$catUsersNum = 0;
		if(is_array($catUsersData)){
			$catUsersNum = count($catUsersData);
		}
		?>
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="card">                
							<div class="card-header">
								<h4 class="card-title"> Cameras</h4>		
								<?php
									//Parking ID
									$parkingId = $_GET['pid']??'';

									//check if the category ID is specified
									$categoryId = $_GET['cid']??"";

									//if parking is not set, go ack and choose parking
									if(!$parkingId)
										header("location:parking");

								?>		
							</div>
							<div class="card-body">
								<div class="toolbar">

									<?php
										if($User->can($currentUserId, 'addUser')){
									?>
										<!--Here you can write extra buttons/actions for the toolbar-->
										<button class="btn btn-info" data-toggle="modal" data-target="#addUser"><i class="now-ui-icons ui-1_simple-add"></i> Add</button>
										<!-- Modal -->
										<div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<form id='addParkingCamera'>
														<div class="modal-header">
															<h5 class="modal-title" id="exampleModalLabel">New parking camera</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<div id="feedBack"></div> 										
															<div class="form-group">
																<select class="selectpicker" id="functionInput" data-style="btn btn-info btn-round" title="Function" data-size="7">
								                                    <option value="entry">Entry </option>
								                                    <option value="exit">Exit</option>
								                                </select>
															</div>
															<div class="form-group">
																<label for="descriptionInput">Alias</label>
																<input type="text" class="form-control" id="aliasInput" aria-describedby="emailHelp" placeholder="Enter short name to refer to this camera">
															</div>

															<div class="form-group">
																<label for="descriptionInput">Description</label>
																<input type="text" class="form-control" id="descriptionInput" aria-describedby="emailHelp" placeholder="Describe this camera">
															</div>
															
															<div class="mt-5">Connectivity</div>
															<hr />
															<div class="form-group">
																<label for="chargeslhInput">Link</label>
																<input type="text" class="form-control" id="URLInput" aria-describedby="emailHelp" min="100" placeholder="IP Address or host">
															</div>
														</div>
														<input type="hidden" name="pid" id="PID" value="<?php echo $parkingId ?>">
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
															<button type="submit" class="btn btn-primary">Create</button>
														</div>
													</form>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
								<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Alias</th>
											<th>Function</th>
											<th>Address</th>
											<th>Created</th>
											<th class="disabled-sorting text-right">Actions</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Alias</th>
											<th>Function</th>
											<th>Address</th>
											<th>Created</th>
											<th class="disabled-sorting text-right">Actions</th>
										</tr>
									</tfoot>
									<tbody>
										<?php
											if($parkingId){
												//get cameras
												$cameras = $Parking->getCameras($parkingId);
												$categories = $Parking->categories($parkingId);

												if($cameras->status){
													$categories = $categories->data;
													foreach ($cameras->data as $key => $camera) {
														$cameraId = $camera["camera"];
														//uSERS of the category
														$cameraDetails = $Camera->details($cameraId);

														//Getting camera address
														$camAddress = "";
														if($cameraDetails->status){
															$camAddress = $cameraDetails->data['address'];
														}
														

														$catUsersData = $catUsers->data;
														$catUsersNum = 0;
														if(is_array($catUsersData)){
															$catUsersNum = count($catUsersData);
														}

														?>
															<tr>
																<td><?php echo $camera['alias']; ?></td>
																<td><?php echo ucfirst($camera['function']); ?></td>
																<td><?php echo $camAddress; ?></td>
																<td><?php echo $camera['createdDate']; ?></td>
																<td class="text-right">
																	<a href="?cid=<?=$categoryId?>" class="btn btn-round btn-info btn-icon btn-sm like"><i class="fas fa-angle-right"></i></a>
																	<a href="#" class="btn btn-round btn-warning btn-icon btn-sm edit"><i class="fas fa-plus"></i></a>
																</td>
															</tr>
														<?php
													}
												}else{
													echo $cameras->msg;
												}

											}
											$users = $User->list();
											
										?>
									</tbody>
								</table>
							</div><!-- end content-->
						</div><!--  end card  -->
					</div>
				</div>
			</div>
			<?php
				$jsFiles = array_merge($jsFiles, array('assets/js/cameras.js'));
	}
?>