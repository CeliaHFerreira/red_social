$(document).ready(function () {
	$(".btn-password").click(function(){
		$.ajax({
			url: URL+'/sendKey',
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});

});