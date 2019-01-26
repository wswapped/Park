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
});