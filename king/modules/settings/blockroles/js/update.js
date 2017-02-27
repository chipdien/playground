$(function(){
	$('.settings #maxPostsPerDay').on('focusout', function() {
		$.post("ajax/roles.php",
		{
			roleid: $( this ).attr('data-roleid'),
			maxPostsPerDay: $( this ).val(),
			action: "update"
		});
	});
	
	$('.settings #maxFbAccounts').on('focusout', function() {
		$.post("ajax/roles.php",
		{
			roleid: $( this ).attr('data-roleid'),
			maxFbAccounts: $( this ).val(),
			action: "update"
		});
	});	
});	
