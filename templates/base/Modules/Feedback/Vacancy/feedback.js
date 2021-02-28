$(function(){
	$('.feedback-form').submit(function(evt){
		evt.preventDefault();
		var form = $(this);
		if (form.hasClass('sending')) return false;
		$('INPUT[name="check_string"]', form).val(form.data('checkstring'));
		$('INPUT[name="hash_string"]', form).val(form.data('hashstring'));
		$('.m-error', form).removeClass('m-error');
		form.addClass('sending');
		$(this).ajaxSubmit({
			url: '/hr-feedback/makeRequest/',
			type: 'post',
			dataType: 'json',
			success: function(res){
				if (res.errors) {
					console.log(res.errors);
					alert('error')
				} else {
					alert('success');
				}
				form.removeClass('sending');
			},
			error: function(){
				form.removeClass('sending');
			}
		});
	});
});