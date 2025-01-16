

$(document).ready(function () {
	$('.confirm').click(function () {
		let msg = $(this).attr('data-confirm');
		return confirm(msg);
	})
})