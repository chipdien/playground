$(function(){

	$('#groupsDatabale').on('click','#deleteSelectedGroups', function(e) {

		groups = [];

		e.preventDefault();

		// Get all checked groups
		$('.checkbox:checked').each(function(){
			groups.push($(this).val());
		});

		$.post("ajax/groupcategory.php",
		{
			category: $("#groupscategory").val(),
			groups: groups,
			action: "deletegroup"
		},
		function(data){
			if(data.status == "success"){
				for (var i = 0; i < groups.length; i++ ) {
					$( "#" + groups[i] ).fadeOut(500, function() { $(this).remove(); });
				}
			}else{
				alertBox(data.message,"danger",false,false);
			}
		});
	});	

});	
