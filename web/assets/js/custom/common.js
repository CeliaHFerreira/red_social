$(document).ready(function () {
	if ($(".label-notifications").text() == 0) {
		$(".label-notifications").addClass("d-none");
	} else {
		$(".label-notifications").removeClass("d-none");
	}
	
	if ($(".label-notifications-msg").text() == 0) {
		$("label-notifications-msg").addClass("d-none");
	} else {
		$(".label-notifications-msg").removeClass("d-none");
	}

	notifications();
	timedFlashMessages();
	
	setInterval(function () {
		notifications();
	}, 60000);
});

function notifications() {
	$.ajax({
		url: URL + '/notifications/get',
		type: 'GET',
		success: function (response) {
			$(".label-notifications").html(response);

			if (response == 0) {
				$(".label-notifications").addClass("d-none");
			} else {
				$(".label-notifications").removeClass("d-none");
			}

		}
	});

	$.ajax({
		url: URL + '/private_message/notification/get',
		type: 'GET',
		success: function (response) {
			$(".label-notifications-msg").html(response);

			if (response == 0) {
				$("label-notifications-msg").addClass("d-none");
			} else {
				$(".label-notifications-msg").removeClass("d-none");
			}

		}
	});
}

function timedFlashMessages() {
	setTimeout(function(){
		$(".flash-message").fadeOut(1500);
	},3000);
}
