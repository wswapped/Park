//User addiotion form handling
$("#addParkingCategory").on('submit', function(e){
	e.preventDefault()
	var name = $("#nameInput").val();
	var parking = $("#PID").val();
	var description = $("#descriptionInput").val();

	var chargesl1h = $("#chargeslhInput").val();
	var chargesl3h = $("#chargesl3hInput").val();
	var chargesm3h = $("#chargesm3hInput").val();

	var fees  = {
		'60':chargesl1h,
		'180':chargesl3h,
		'0':chargesm3h //defaults charges, more than 3 hours
	}

	// alert();
	//checking if all fields are set
	if(Boolean(name) && Boolean(description) && Boolean(chargesl1h) && Boolean(chargesl3h) && Boolean(chargesm3h) ){
		//here we can submit
		$.post(apiLink, {action:'addParkingCategory', name:name, description:description, parking:parking, 'fees':fees, userId:currentUserId}, function(ret){
				if(ret.status){
					location.reload();
				}else{
					$('#feedBack').append("<p class='text-danger'>"+ret.msg+"</p>");
				}
		})
	}else{
		$('#feedBack').append("<p class='text-danger'>Please fill in all required fields</p>");
	}
});