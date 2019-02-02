//camera addiotion form handling
$("#addParkingCamera").on('submit', function(e){
	e.preventDefault()
	var functionIn = $("#functionInput").val();
	var description = $("#descriptionInput").val();
	var parking = $("#PID").val();

	var URLInput = $("#URLInput").val();

	//checking if all fields are set
	if(Boolean(functionIn) && Boolean(description) && Boolean(URLInput)){
		//here we can submit
		$.post(apiLink, {action:'addParkingCamera', 'usage':functionIn, address:URLInput, description:description, parking:parking, userId:currentUserId}, function(ret){
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