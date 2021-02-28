$(function() {
	require(['ui', 'message'], function(ui, message) {
		
		$('.actions-panel .action-save').on('click', function() {
			ui.form.submit($('.social-auth-form'), {
				datatype: 'html',
				success: function(res) {
					message.ok('Настройки сохранены.');
				},
				errors: function(errors) {
					message.errors({errors: errors});
				},
				serverError: function(err) {
					message.serverErrors(err);
				}
			});
			return false;
		});
		
		ui.dropdown($('.soc-visible'), {
			select: function() {
				var val = $(this).data('val');
				var dd = $(this).closest('.soc-visible');
				var input = dd.prev('INPUT');
				if (val) {
					dd.removeClass('m-hide');
				} else {
					dd.addClass('m-hide');
				}
				input.val(val);
			}
		});
		
	});
});