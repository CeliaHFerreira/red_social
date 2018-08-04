$(document).ready(function () {
	var ias = jQuery.ias({
		container: '.notification-box #box-content',
		item: '.notification-item',
		pagination: '.notification-box .pagination',
		next: '.notification-box .pagination .next_link',
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
		text: '·'
	}));

});


