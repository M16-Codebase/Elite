$(function() {
	require(['ui', 'userForm', 'popupAlert'], function(ui, userForm, popupAlert) {
		
		// выравнивание колонок
		var bigCol, smallCol;
		var col1 = $('.contacts-block .contacts-form');
		var col2 = $('.contacts-block .contacts-info');
		if (col1.outerHeight(true) > col2.outerHeight(true)) {bigCol = col1; smallCol = col2;}
		else {bigCol = col2; smallCol = col1;}
		smallCol.height(function() {
			return smallCol.height() + bigCol.outerHeight(true) - smallCol.outerHeight(true);
		});
		
		// форма
		userForm.init($('.feedback-form'), {
			beforeSubmit: function() {
				$('.m-error', this).removeClass('m-error');
				$('.f-error, .f-error LI', this).addClass('a-hidden');
			},
			success: function(res) {
				popupAlert.ok({
					title: 'Сообщение принято',
					text: 'Спасибо за обращение в нашу компанию. Мы свяжемся с Вами в ближайшее время.'
				});
				$(this).clearForm();
			},
			errors: function(err) {
				for (var i in err) {
					if (i === 'check_sum') {
						$('.general-err', this).removeClass('a-hidden').find('.e-check_sum').removeClass('a-hidden');
					} else {
						$('INPUT[name="' + i + '"]', this).closest('.field').addClass('m-error').find('.f-error.e-' + err[i]).removeClass('a-hidden');						
					}
					ui.scrollTo($('.m-error:first', this));
				}
			},
			serverError: function(err) {
				popupAlert.error({
					text: 'Не удалось отправить заявку.',
					errors: 'Ошибка сервера: ' + err.status
				});
			}
		});
		
	});
});