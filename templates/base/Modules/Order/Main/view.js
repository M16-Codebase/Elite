$(function() {
	
	// попап перехода на onpay
	$('.order-pay-button .a-button-red').bind('click', function() {
		$('.popup-onpay').dialog({width: 435}).dialog('open');
		return false;
	});
	
	// ссылка "что делать дальше"
	$('.order-help').each(function() {
		var stepsPopup = $('.steps-popup');
		$('.tip-icon', this).bind('mouseenter', function() {
			stepsPopup.slideDown('fast');
		});
		stepsPopup.bind('mouseleave', function() {
			stepsPopup.slideUp('fast');
		});
	});
	
});