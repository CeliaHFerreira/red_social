$(document).ready(function () {
	var ias = jQuery.ias({
		container: '.default-box #user-melodies',
		item: '.melody-item',
		pagination: '.default-box .pagination',
		next: '.pagination .next_link',
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
		text:''
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
		var urlRedirect = URL + '/home';
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
								window.location.replace(urlRedirect);
							}
						});
					}
				},
				cancel: {
					label: 'No',
					className: 'btn-danger',
					callback: function (result) {
						console.log('Respuesta ' + result);
					}
				}
			}

		});
	});

	//eliminar valoración
	$(".btn-delete-comment").unbind('click').click(function () {
		$(this).parent().parent().parent().addClass("d-none");
		$.ajax({
			url: URL + '/delete_assest/' + $(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
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
			success: function(response){
				console.log(response);
			}
		});
	});
	
	//desmarcar me gusta (ya no me gusta)
	$(".btn-unlike").unbind('click').click(function (){
		$(this).addClass("d-none");
		$(this).parent().find('.btn-like').removeClass("d-none");
		$.ajax({
			url: URL + '/unlike/' + $(this).attr("data-id"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});
	
	
	//puntuar melodía
	$(".a-star").unbind('click').click(function () {
		$(this).parent().addClass("d-none");
		$(this).parent().parent().find('.ec-scored').removeClass("d-none");
		$.ajax({
			url: URL + '/score/' + $(this).attr("data-id") + '/' + $(this).attr("data-value"),
			type: 'GET',
			success: function(response){
				console.log(response);
			}
		});
	});
}


