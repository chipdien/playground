$(function(){
	$('#groupsDatabale').on('click','#addCategory', function(e) {
		e.preventDefault();
		$.post("ajax/groupcategory.php",
		{
			categoryname: $("#newCategoryName").val(),
			action: "addcategory"
		},
		function(data){
			if(data.status == "success"){
				alertBox(data.message,"success",false,true,true);		
			}else{
				alertBox(data.message,"danger",false,true,true);		
			}
		});
	});	

});	
