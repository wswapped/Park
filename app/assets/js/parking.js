$(document).ready(function() {
	var table = $('#datatable').DataTable({
		"order": [[ 2, "desc" ]]
	});

	// Edit record
	table.on( 'click', '.edit', function () {
	  $tr = $(this).closest('tr');
	  if($($tr).hasClass('child')){
		$tr = $tr.prev('.parent');
	  }

	  var data = table.row($tr).data();
	  alert( 'You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.' );
	} );

	// Delete a record
	table.on( 'click', '.remove', function (e) {
	  $tr = $(this).closest('tr');
	  if($($tr).hasClass('child')){
		$tr = $tr.prev('.parent');
	  }
	  table.row($tr).remove().draw();
	  e.preventDefault();
	} );
});