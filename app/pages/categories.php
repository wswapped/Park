<?php
	//check if we are viewing specific category
	$categoryId = $_GET['cid']??"";

	//TODO: Check if the user is allowed to access this category
	if(!empty($categoryId)){
		include_once "viewCategory.php";
	}else{
		?>
			<div class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="card">                
							<div class="card-header">
								<h4 class="card-title"> Categories</h4>		
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
													<form id='addParkingCategory'>
														<div class="modal-header">
															<h5 class="modal-title" id="exampleModalLabel">New parking category</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<div id="feedBack"></div> 										
															<div class="form-group">
																<label for="nameInput">Name</label>
																<input type="text" class="form-control" id="nameInput" aria-describedby="emailHelp" placeholder="Category name">
															</div>
															<div class="form-group">
																<label for="descriptionInput">Description</label>
																<input type="text" class="form-control" id="descriptionInput" aria-describedby="emailHelp" placeholder="Describe this category">
															</div>
															
															<div class="mt-5">Category charges</div>
															<hr />
															<div class="form-group">
																<label for="chargeslhInput">Less than hour</label>
																<input type="number" class="form-control" id="chargeslhInput" aria-describedby="emailHelp" min="100" placeholder="Charges in FRW">
															</div>
															<div class="form-group">
																<label for="descriptionInput">Less than 3 hours</label>
																<input type="number" class="form-control" id="chargesl3hInput" aria-describedby="emailHelp" placeholder="Charges in FRW" min="100">
															</div>
															<div class="form-group">
																<label for="descriptionInput">More than 3 hours</label>
																<input type="number" class="form-control" id="chargesm3hInput" aria-describedby="emailHelp" placeholder="Charges in FRW" min="100">
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
											<th>Name</th>
											<th>Members</th>
											<th>Created</th>
											<th class="disabled-sorting text-right">Actions</th>
										</tr>
									</thead>
									<tfoot>
										<tr>
											<th>Name</th>
											<th>Members</th>
											<th>Created</th>
											<th class="disabled-sorting text-right">Actions</th>
										</tr>
									</tfoot>
									<tbody>
										<?php
											if($parkingId){
												//get categories
												$categories = $Parking->categories($parkingId);

												if($categories->status){
													$categories = $categories->data;
													foreach ($categories as $key => $category) {
														$categoryId = $category['id'];
														$pricing = "";
														?>
															<tr>
																<td><?php echo $category['name']; ?></td>
																<td><?php echo 0 ?></td>
																<td><?php echo $category['createdDate']; ?></td>
																<td class="text-right">
																	<a href="?cid=<?=$categoryId?>" class="btn btn-round btn-info btn-icon btn-sm like"><i class="fas fa-angle-right"></i></a>
																	<a href="#" class="btn btn-round btn-warning btn-icon btn-sm edit"><i class="fas fa-plus"></i></a>
																</td>
															</tr>
														<?php
													}
												}else{
													echo $categories->msg;
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
				$jsFiles = array_merge($jsFiles, array('assets/js/parkingCategories.js'));
	}
?>