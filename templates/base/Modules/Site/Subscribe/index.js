$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		var errorsList = {
			"auth:wrong_credentials":"Неверные данные авторизации",
			"auth:force_change_password":"Пароль устарел. Смените пароль в личном кабинете на сайте pro.subscribe.ru, укажите новый пароль в форме ниже и попробуйте снова.",
		};
		$('.actions-panel .action-button.action-save').click(function() {
			ui.form.submit($("#auth-data-form"),
				{
					success: function(res){
						$('.viewport').html(res.content);
						editContent.close();
						$(window).resize();
						ui.initAll();
					},
					errors: function(errors) {
						var error = errors[0].key + ':' + errors[0].error;
						message.errors({errors: errorsList[error]});
					},
					servererror: function(err) {
						message.errors({
							text: 'Ошибка сервера: ' + err.status,
							descr: (err.status === 404 || err.status === 200)? '' : err.responseText
						});
					}
				}
			);
		});
		
	});
});
////$(function(){
//	$('.actions-panel .action-save').click(function(){
//		$('#auth-data-form').trigger('submit');
//	});
//	
//	$('#auth-data-form').submit(function(evt){
//		evt.preventDefault();
//		$(this).ajaxSubmit({
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					alert('Данные сохранены');
//				} else {
//					if( 'auth' in res.errors ) {
//						alert('Указаны неверные учетные данные');
//					} else {
//						alert('Заполните все поля');
//					}
//				}
//			}
//		});
//	});
//});