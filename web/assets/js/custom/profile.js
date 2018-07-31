$(document).ready(function () {
	var ias = jQuery.ias({
		container: '.profile-box #user-melodies',
		item: '.melody-item',
		pagination: '.profile-box .pagination',
		next: '.profile-box .pagination .next_link',
		triggerPageThreshold: 5
	});

	ias.extension(new IASTriggerExtension({
		text: 'Ver más',
		offset: 3
	}));

	ias.extension(new IASSpinnerExtension({
		src: URL + '/../assets/images/ajax-loader.gif'
	}));

	ias.extension(new IASNoneLeftExtension({
		text: 'No hay más melodías para mostrar'
	}));

	ias.on('ready', function (event) {
		buttons();
	});

	ias.on('rendered', function (event) {
		buttons();
	});

});

function buttons() {
	//eliminar melodía
	$(".btn-delete-melody").click(function () {
		var parentp = $(this).parent().parent().parent();
		var urlMelody = URL + '/melody/remove/';
		var data = $(this).attr("data-id");
		bootbox.dialog({
			message: "¿Está seguro de borrar la melodía?",
			buttons: {
				confirm: {
					label: 'Si',
					className: 'btn-success',
					callback: function (result) {
						console.log('Respuesta ' + result);
						parentp.addClass('d-none');
						$.ajax({
							url: urlMelody+data,
							type: 'GET',
							success: function (response) {
								console.log(response);
							}
						});
					}
				},
				cancel: {
					label: 'No',
					className: 'btn-danger',
					callback: function (result) {
						console.log('Respuesta ' + result);
						parent.fadeOut('slow');
					}
				}
			}

		});
	});
	
	//eliminar cuenta
	$(".btn-delete-user").click(function () {
		var urlUser = URL + '/remove/';
		bootbox.dialog({
			message: "¿Está seguro de eliminar su cuenta de usuario? No podrá deshacer esta opción",
			buttons: {
				confirm: {
					label: 'Si',
					className: 'btn-success',
					callback: function (result) {
						console.log('Respuesta ' + result);
						$.ajax({
							url: urlUser,
							success: function (response) {
								console.log(response);
								window.location.replace("http://google.es");
							}
						});
					}
				},
				cancel: {
					label: 'No',
					className: 'btn-danger',
					callback: function (result) {
						console.log('Respuesta ' + result);
						parent.fadeOut('slow');
					}
					
				}
			}

		});
	});

	//marcar me gusta
	$(".btn-like").unbind('click').click(function () {
		$(this).addClass("d-none");
		$(this).parent().find('.btn-unlike').removeClass("d-none");
		$.ajax({
			url: URL + '/like/' + $(this).attr("data-id"),
			type: 'GET',
			success: function (response) {
				console.log(response);
			}
		});
	});

	//desmarcar me gusta (ya no me gusta)
	$(".btn-unlike").unbind('click').click(function () {
		$(this).addClass("d-none");
		$(this).parent().find('.btn-like').removeClass("d-none");
		$.ajax({
			url: URL + '/unlike/' + $(this).attr("data-id"),
			type: 'GET',
			success: function (response) {
				console.log(response);
			}
		});
	});
	
	//seguir usuario
	$(".btn-follow").unbind("click").click(function(){
		$(this).addClass("d-none");
		$(this).parent().find(".btn-unfollow").removeClass("d-none");
		$.ajax({
			url: URL+'/follow',
			type: 'POST',
			data: {followed: $(this).attr("data-followed")},
			success: function(response){
				console.log(response);
			}
		});
	});
	
	//dejar de seguir a un usuario
	$(".btn-unfollow").unbind("click").click(function(){
		$(this).addClass("d-none");
		$(this).parent().find(".btn-follow").removeClass("d-none");
		$.ajax({
			url: URL+'/unfollow',
			type: 'POST',
			data: {followed: $(this).attr("data-followed")},
			success: function(response){
				console.log(response);
			}
		});
	});
}


