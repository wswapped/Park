<div class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="card">                
				<div class="card-header">
					<h4 class="card-title"> My Parkings</h4>
				</div>
				<div class="card-body">

					<div class="toolbar">
						<!--        Here you can write extra buttons/actions for the toolbar              -->
					</div>
					<table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Name</th>
								<th>Location</th>
								<th>Total capacity</th>
								<th>Cameras</th>
								<th class="disabled-sorting text-right">Actions</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Name</th>
								<th>Location</th>
								<th>Total capacity</th>
								<th>Cameras</th>
								<th class="disabled-sorting text-right">Actions</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
								$userParking = $Parking->userList($currentUserId);
								foreach ($userParking as $key => $park) {
									$parkId = $park['id'];
									$parkingData = $Parking->details($park['id']);
									$pname = $parkingData['name'];
									$plocation = $parkingData['location'];
									$pcapacity = $parkingData['capacity'];

									//cameras
									$cameras = $Parking->getCameras($parkId);

									$nCameras = 0;
									if($cameras->status){
										$nCameras = count($cameras->data);
									}
									?>
										<tr>
											<td><?php echo $pname; ?></td>
											<td><?php echo $plocation; ?></td>
											<td><?php echo $pcapacity; ?></td>
											<td><a href="cameras?pid=<?=$parkId?>"><?=$nCameras?></a></td>
											<td class="text-right">
												<a href="categories?pid=<?=$parkId?>" class="btn btn-round btn-info btn-icon btn-sm"><i class="fas fa-angle-right"></i></a>
												<a href="#" class="btn btn-round btn-warning btn-icon btn-sm edit"><i class="fas fa-plus"></i></a>
											</td>
										</tr>
									<?php
								}
							?>
						</tbody>
					</table>
				</div><!-- end content-->
			</div><!--  end card  -->
		</div>
	</div>
</div>
<?php
	$jsFiles = array_merge($jsFiles, array('assets/js/parking.js'));
?>