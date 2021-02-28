$(function(){
	require(['ui', 'popupAlert'], function(ui, popupAlert) {		
	
		$('.popup-change_country FORM').submit(function() {
			$(this).ajaxSubmit({
				success: function(res) {
					if (res.errors) {
						popupAlert.error({
							text: 'Не удалось изменить страну.',
							errors: _.values(res.errors)
						});
						$('.popup-change_country').dialog('close');
					} else window.location.reload();
				},
				error: function(err) {
					popupAlert.error({
						text: 'Не удалось изменить страну.',
						errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
						errtext: $('BODY').data('admin')? err.responseText : ''
					});
					$('.popup-change_country').dialog('close');
				}
			});
			return false;
		});
		$('.changeCountry').click(function(){
			var popup = $('.popup-change_country');
			var tr = $(this).closest('TR');
			$('FORM', popup).clearForm();
			$('INPUT[name="city_id"]', popup).val(tr.data('city_id'));
			$('SELECT[name="country_id"] OPTION[value="' + tr.data('country_id') + '"]').attr('selected', 'selected');
			$('.chosen', popup).trigger('liszt:updated');
			popup.dialog('open');
		});
	
	});
});