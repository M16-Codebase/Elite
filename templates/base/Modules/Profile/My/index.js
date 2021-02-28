$(function() {
	require(['ui', 'popupForm', 'popupAlert'], function(ui, popupForm, popupAlert) {
		
		$('.profile-edit-popup').each(function() {
			var popupTitle = $(this).data('title');
			var cont = $('FORM', this).data('cont');
			popupForm($('FORM', this), {
				open: function() {
					$('INPUT', this).each(function() {
						var field = $(this).attr('name');
						if (!$('.user-info.' + field).length) return;
						$(this).val($('.user-info.' + field).text());
					});
				},
				errors: function(errors) {
					if (errors.email) {
						$('.errors', this).append('<li>Некорректный адрес электронной почты</li>');
						$('INPUT[name="email"]', this).closest('.field').addClass('m-error');
					} 
					if (errors.inn) {
						$('.errors', this).append('<li>Некорректный номер ИНН</li>');
						$('INPUT[name="inn"]', this).closest('.field').addClass('m-error');
					}
					if (errors.pass) {
						$('.errors', this).append('<li>Пожалуйста, введите новый пароль</li>');
						$('INPUT[name="pass"]', this).closest('.field').addClass('m-error');
					} else if (errors.pass2) {
						$('.errors', this).append('<li>Введенные пароли не совпадают</li>');
						$('INPUT[name="pass"]', this).closest('.field').addClass('m-error');
						$('INPUT[name="pass2"]', this).closest('.field').addClass('m-error');
					}
				},
				success: function(res) {
					if (cont) $(cont).html(res.content);
					if ($('.user-info.name', cont)) {
						$('.header-name').text($('.user-info.name', cont).text());
						$('.header-surname').text($('.user-info.surname', cont).text());
					}
					ui.initAll();
					popupAlert.ok({
						title: popupTitle,
						text: 'Ваши данные успешо изменены'
					});
				}
			});
		});		
		
		// удалить адрес
		$('.address-list').delegate('.delete', 'click', function() {
			var id = $(this).data('id');
			popupAlert.confirm({
				title: 'Удалить адрес?',
				okText: 'Удалить',
				ok: function() {
					$.post('/profile/delAddress/', {id: id}, function(res) {
						if (res.errors) {
							alert('Ошибка');
						} else {
							$('.address-list').html(res.content);
						}					
					}, 'json');
				}
			});			
			return false;
		});
		
		// подписки
		$('.subscr-cbx').change(function() {
			var subscr = $(this).is(':checked')? 1 : 0;
			$.post('/profile/setSubscribe/', {subscribe: subscr}, function(res) {				
			});
		});
		$('.order-status-cbx').change(function() {
			var subscr = $(this).is(':checked')? 1 : 0;
			$.post('/profile/setOrderStatus/', {order_status: subscr}, function(res) {				
			});
		});

        $('.social-auth-detach .detach-btn').click(function(){
            var btn = $(this);
            var network_block = btn.parents('li');
            $.ajax({
                url: '/profile/detachAuth/',
                type: 'post',
                data: {
                    network: btn.data('network')
                },
                dataType: 'json',
                success: function(res){
                    if (res.errors === null){
                        network_block.remove();
                    }
                }
            });
            return false;
        });
		
	});
});

function uLoginAttachAccount(token){
    $.ajax({
        url: '/profile/attachAuth/',
        type: 'post',
        data: {
            token: token
        },
        dataType: 'json',
        success: function(res){
            console.log(res);
        }
    })
}

