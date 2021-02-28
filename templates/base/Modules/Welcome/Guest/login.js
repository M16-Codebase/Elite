$(function(){
//	require(['ui'], function(ui) {
//		ui.form('.user-form', {
//			afterSubmit  :function(res) {
//				console.log(res);
//			},
//		});
//	});
	$('.auth-body').on('focus', 'input', function(){
		$(this).closest('.auth-body').find('.btn').removeClass('error').removeAttr('disabled');
	});
})