$(function(){
	$groups = [];

	$('#groupsDatabale').on('click','.addToCategory', function() {
		// Clear message box
		$(".addCateMsgBoxModal").html("");
		// and finally show the modal
		$( '#addToCategoryModal' ).modal({ show: true });
		return false;
	});	

	$('#groupsDatabale').on('click','#addSelectedGroups', function() {
		// Get all checked groups
		groups = [];
		$('.checkbox:checked').each(function(){
			groups.push($(this).val());
		});
		// Clear message box
		$(".addCateMsgBoxModal").html("");
		// and finally show the modal
		$( '#addToCategoryModal' ).modal({ show: true });
		return false;
	});	

	$('#modalAddCateBtn').click(function() {
		// Clear message box
		$(".addCateMsgBoxModal").html("");
		category = $('.groupscategories', '#addToCategoryModal').val();

		$("#modalAddCateBtn").prop('disabled', false);
		$.post("ajax/groupcategory.php",
		{
			category: category,
			groups: groups,
			action: "addgroup"
		},
		function(data){
			if(data.status == "success"){
				alertBox(data.message,"success",".addCateMsgBoxModal",false);
			}else{
				alertBox(data,"danger",".addCateMsgBoxModal",false);
			}
		});

		$("#modalAddCateBtn").prop('disabled', true);
		
	});

});	
