$(document).ready(function(){
	// initialise Datetimepicker
	if($(".datetimepicker").length != 0){
		$('.datetimepicker').datetimepicker({
			icons: {
				  time: "now-ui-icons tech_watch-time",
				  date: "now-ui-icons ui-1_calendar-60",
				  up: "fa fa-chevron-up",
				  down: "fa fa-chevron-down",
				  previous: 'now-ui-icons arrows-1_minimal-left',
				  next: 'now-ui-icons arrows-1_minimal-right',
				  today: 'fa fa-screenshot',
				  clear: 'fa fa-trash',
				  close: 'fa fa-remove'
			}
		});
	}

	//Adding memner form
	var addCategoryMemberFormElem = $("#addCategoryMemberForm")
	addCategoryMemberFormElem.on('submit', function(ev){
		ev.preventDefault();

		var plate = $("#carPlateInput").val();
		var expiryDate = $("#expiryDateInput").val();

		//checking if all fields are set
		if(Boolean(plate) && Boolean(expiryDate)){
			//here we can submit
			$.post(apiLink, {action:'addCategoryMember', plate:plate, expiryDate:expiryDate, category:currentCategoryId, userId:currentUserId}, function(ret){
					if(ret.status){
						location.reload();
					}else{
						console.log($(this))
						addCategoryMemberFormElem.find('.feedBack').append("<p class='text-danger'>"+ret.msg+"</p>");
					}
			})
		}else{
			$('#feedBack').append("<p class='text-danger'>Please fill in all required fields</p>");
		}
	})
});