

$(document).ready(function () {
	$('.confirm').click(function () {
		let msg = $(this).attr('data-confirm');
		return confirm(msg);
	})
})

/** Product */
$(document).ready(function () {
	$('body.product .add-discount-row').click(function() {
		$('.discount-rules').append($('.discount-rules .template').prop('outerHTML'));
	})

	$('body.product .discount-rule-row .remove').click(function() {
		console.log("REMOVING: " + $(this).closest('.discount-rule-row').html());
		$(this).closest('.discount-rule-row').remove();

	})
})

