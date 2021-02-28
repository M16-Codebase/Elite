$(function(){
	require(['ui', 'popupAlert', 'popupForm'], function(ui, popupAlert, popupForm) {
		
		// удаление тегов в поиске
		$('.search-form').each(function() {			
			var btnRemove = $('.btn-remove', this);
			var input = $('INPUT', this);
			btnRemove.click(function() {
				input.val('');
				return false;
			});
		});
		
		$('.post-cont').on('click', '.remove-comment', function(){
			var id = $(this).data('id');
			popupAlert.confirm({
				title: 'Удаление',
				text: 'Вы уверены, что хотите удалить комментарий?',
				okText: 'Удалить',
				ok: function() {
					$.ajax({
						url: '/blog/removeComment/',
						type: 'post',
						data: {id: id},
						dataType: 'json',
						success: function(data){
							if (data.errors){

							} else {
								$('.comments-cont').html(data.content);
								ui.initAll();
								popupAlert.ok({
									title: 'Ваш комментарий',
									text: 'Ваш комментарий удален'
								});
							}
							return false;
						}
					});
				}
			});			
			return false;
		});

		// открытие Popup с добавлением коммента
		$('.post-cont').each(function() {
			var cont = $(this);
			if ($('.comments-cont', cont).hasClass('active-comment')) {
				var popup = $('.popup-add-comment');
				popup.dialog('open');
			}
		});
	
		// popup-add-comment	
		popupForm($('.popup-add-comment FORM'), {
			open: function() {
				$('.m-error', this).removeClass('m-error');
				$('.f-error, .f-error LI', this).addClass('a-hidden');
			},
			errors: function(err) {
				for (var i in err) {
					if (i === 'check_sum') {
						$('.general-err', this).removeClass('a-hidden').find('.e-check_sum').removeClass('a-hidden');
					} else {
						$('[name="' + i + '"]', this).closest('.field').addClass('m-error').find('.f-error.e-' + err[i]).removeClass('a-hidden');						
					}
					ui.scrollTo($('.m-error:first', this));
				}
			},
			success: function(data) {
				$('.comments-cont').html(data.content);
				ui.initAll();
				popupAlert.ok({
					title: 'Добавление комментария',
					text: 'Ваш комментарий принят.'
				});
			},
			serverError: function(err) {
				popupAlert.error({
					text: 'Не удалось отправить комментарий.',
					errors: 'Ошибка сервера: ' + err.status
				});
			}
		});
		
				
	});
	
});