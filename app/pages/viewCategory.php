<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">                
				<div class="card-header">
					<?php
						//Page should be loaded with category
						if(!$categoryId){
							header("location:parking");
							die();
						}

						//fetch the information of the category
						$catData = $Parking->getCategory($categoryId);

						if(!$catData->status){
							echo "$catData->msg";
							die();
						}
						$catData = $catData->data;

						//uSERS of the category
						$catUsers = $Parking->getCategoryMembers($categoryId);

						$catUsersData = $catUsers->data;
						$catUsersNum = 0;
						if(is_array($catUsersData)){
							$catUsersNum = count($catUsersData);
						}

						$feesData = $catData['fees'];
						$catName = $catData['name'];
						$catDescription = $catData['description'];
					?>
					<h4 class="card-title">
						<span><?=$catName?></span><br />
						<span><small></small></span>
					</h4>	
					<p><?=$catDescription?></p>	
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<p>Number of plans</p>
						</div>
						<div class="col-md-4">
							<p>Users - (<?=$catUsersNum?>)</p>
						</div>
						<div class="col-md-4">
							<p>Income generated</p>
						</div>
					</div>
					<div class="toolbar">
						<?php
							if($User->can($currentUserId, 'addUser')){
						?>
							<!--Here you can write extra buttons/actions for the toolbar-->
							<!-- <button class="btn btn-info" data-toggle="modal" data-target="#addUser"><i class="now-ui-icons ui-1_simple-esdit objects_key"></i> Edit</button> -->
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
				</div><!-- end content-->
			</div><!--  end card  -->
		</div>

		<div class="col-md-6">
			<div class="card">                
				<div class="card-header">
					<h4 class="card-title">
						<span>Fees & Plans</span><br />
						<span><small></small></span>
					</h4>	
					<p><small>Payment plans and models for users in this category</small></p>	
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
					<?php
						if($feesData->status){

							//check the numbers
							$feesData = $feesData->data;
							if(count($feesData) > 0){
								?>
									<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Duration</th>
												<th>Fee</th>
												<th>Usage</th>
												<th class="disabled-sorting text-right">Actions</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>Duration</th>
												<th>Fee</th>
												<th>Usage</th>
												<th class="disabled-sorting text-right">Actions</th>
											</tr>
										</tfoot>
										<tbody>
											<?php
												foreach ($feesData as $key => $plan) {
													$pricing = "";
													?>
														<tr>
															<td><?php echo $plan['duration']; ?></td>
															<td><?php echo  $plan['fee'] ?></td>
															<td><?php echo 0 ?></td>
															<td class="text-right">
																<a href="?cid=<?=$categoryId?>" class="btn btn-round btn-info btn-icon btn-sm like"><i class="fas fa-angle-right"></i></a>
																<a href="#" class="btn btn-round btn-warning btn-icon btn-sm edit"><i class="fas fa-plus"></i></a>
															</td>
														</tr>
													<?php
												}
											?>
										</tbody>
									</table>
								<?php

							}else{
								echo "<p class='text-warning'>No Plans available</p>";
							}

						}else{
							?>
								<p class="text-danger"><?=$feesData->msg?></p>
							<?php
						}
						
					?>
					
				</div><!-- end content-->
			</div><!--  end card  -->
		</div>

		<div class="col-md-6">
			<div class="card">                
				<div class="card-header">
					<h4 class="card-title">
						<span>Members</span><br />
						<span><small></small></span>
					</h4>	
					<p><small>Cars and people using being charged as per this category</small></p>	
				</div>
				<div class="card-body">
					<div class="toolbar">

						<?php
							if($User->can($currentUserId, 'addCategoryMember')){
						?>
							<!--Here you can write extra buttons/actions for the toolbar-->
							<button class="btn btn-info" data-toggle="modal" data-target="#addCategoryMember"><i class="now-ui-icons ui-1_simple-add"></i> Add</button>
							<!-- Modal -->
							<div class="modal fade" id="addCategoryMember" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<form id='addCategoryMemberForm'>
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">New category member</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<div class="feedBack"></div> 										
												<div class="form-group">
													<label for="nameInput">Plate</label>
													<input type="text" class="form-control" id="carPlateInput" aria-describedby="emailHelp" placeholder="Enter Plate number">
												</div>
												<div class="form-group">
													<label for="descriptionInput">Expiry date</label>
													<input type="text" class="form-control datetimepicker" id="expiryDateInput" placeholder="Date the user should use category" value="<?php echo date(STANDARD_DATETIME_FORMAT, time()+(3600*24)) ?>">
												</div>
												<!-- <div class="form-group">
													<label for="descriptionInput">Description</label>
													<input type="text" class="form-control" id="descriptionInput" aria-describedby="emailHelp" placeholder="Describe this category">
												</div>
												 -->
											</div>
											<input type="hidden" name="pid" id="PID" value="<?php echo $parkingId ?>">
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
												<button type="submit" class="btn btn-primary">Add</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php

						if($catUsers->status){

							if($catUsersNum > 0){
								?>
									<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Car plate</th>
												<th>Expiring Date</th>
												<th>Usage</th>
												<th class="disabled-sorting text-right">Actions</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th>Car plate</th>
												<th>Expiring Date</th>
												<th>Usage</th>
												<th class="disabled-sorting text-right">Actions</th>
											</tr>
										</tfoot>
										<tbody>
											<?php
												foreach ($catUsersData as $key => $user) {
													$plateData = $Parking->carPlate($user['car']);
													?>
														<tr>
															<td><?php echo $plateData->status?$plateData->data:$plateData->msg; ?></td>
															<td><?php echo  $user['expiryDate'] ?></td>
															<td><?php echo 0 ?></td>
															<td class="text-right">
																<a href="?cid=<?=$categoryId?>" class="btn btn-round btn-info btn-icon btn-sm like"><i class="fas fa-angle-right"></i></a>
																<a href="#" class="btn btn-round btn-warning btn-icon btn-sm edit"><i class="fas fa-plus"></i></a>
															</td>
														</tr>
													<?php
												}
											?>
										</tbody>
									</table>
								<?php

							}else{
								echo "<p class='text-warning'>No members yet</p>";
							}

						}else{
							?>
								<p class="text-danger"><?=$catUsers->msg?></p>
							<?php
						}
						
					?>
					
				</div><!-- end content-->
			</div><!--  end card  -->
		</div>

	</div>
</div>
<script type="text/javascript">
	const currentCategoryId = <?=$categoryId?>;
</script>
<?php
	$jsFiles = array_merge($jsFiles, array('assets/js/plugins/bootstrap-datetimepicker.js', 'assets/js/viewCategory.js'));
?>