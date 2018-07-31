$(document).ready(function(){
	$(".username-input").blur(function(){
		var username = this.value;
		$.ajax({
			url: URL+'/username_test',
			data: {username: username},
			type: 'POST',
			success: function(response){
				if(response == "used"){
					$(".username-input").css("border", "1px solid red");
				}else{
					$(".username-input").css("border", "1px solid green");
				}
			}
		});
	});
});