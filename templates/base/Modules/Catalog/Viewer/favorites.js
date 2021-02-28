$(function() {
	require(['popupAlert'], function(popupAlert) {
		var selForm = $('.items_edit.selection');
		
		// удалить всё
		$('.action-delete').click(function() {
			popupAlert.confirm({
				title: 'Выборка',
				text: 'Вы уверены, что хотите удалить все предложения из выборки?',
				okText: 'Удалить',
				ok: function() {
					$('.offers-item .offer-cbx :checkbox').attr('checked', true);
					selForm.ajaxSubmit({
						url: '/catalog-view/removeFromFavorites/',
						dataType: 'json',
						success: function(res) {
							if (res.errors) {
								popupAlert.error({
									title: 'Выборка',
									text: 'Предложения не были удалены из выборки.',
									errors: _.values(res.errors)
								});
							} else {
								window.location.reload();
							}
						},
						error: function(err) {
							popupAlert.error({
								title: 'Выборка',
								text: 'Предложения не были удалены из выборки.',
								errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
								errtext: $('BODY').data('admin')? err.responseText : ''
							});
						}
					});					
				}
			});
			return false;
		});
		
		// удаление чекбоксами
		$('.offer-item .offer-header :checkbox').each(function() {
			var itemCbx = $(this);
			var varCbx = $(this).closest('.offer-item').find('.offers-item .offer-cbx :checkbox');
			var check = function() {
				if (varCbx.length === varCbx.filter(':checked').length) itemCbx.attr('checked', true);
				else itemCbx.attr('checked', false);
			};
			itemCbx.change(function() {
				if ($(this).is(':checked')) varCbx.attr('checked', true);
				else varCbx.attr('checked', false);
			});
			varCbx.change(check);
			check();
		});
		$('.action-no_selection').click(function() {
			if ($(this).hasClass('m-inactive')) return false;
			popupAlert.confirm({
				title: 'Выборка',
				text: 'Вы уверены, что хотите удалить выбранные предложения из выборки?',
				okText: 'Удалить',
				ok: function() {
					selForm.ajaxSubmit({
						url: '/catalog-view/removeFromFavorites/',
						dataType: 'json',
						success: function(res) {
							if (res.errors) {
								popupAlert.error({
									title: 'Выборка',
									text: 'Предложения не были удалены из выборки.',
									errors: _.values(res.errors)
								});
							} else {
								window.location.reload();
							}
						},
						error: function(err) {
							popupAlert.error({
								title: 'Выборка',
								text: 'Предложения не были удалены из выборки.',
								errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
								errtext: $('BODY').data('admin')? err.responseText : ''
							});
						}
					});
				}
			});
			return false;
		});
		
		// удаление кнопками
		selForm.delegate('.offer-selection', 'click', function() {
			var variant = $(this).closest('.offers-item').length;
			if (variant) {
				var req = {
					variant_id: $(this).closest('.offers-item').data('id')
				};
			} else {
				var id = [];
				$(this).closest('.offer-item').find('.offers-item').each(function() {
					id.push($(this).data('id'));
				});
				var req = {
					variants: id
				};
			}
			popupAlert.confirm({
				title: 'Выборка',
				text: variant? 'Вы уверены, что хотите удалить предложение из выборки?' : 'Вы уверены, что хотите удалить объект из выборки?',
				okText: 'Удалить',
				ok: function() {
					$.post('/catalog-view/removeFromFavorites/', req, function(res) {
						if (res.errors) {
							popupAlert.error({
								title: 'Выборка',
								text: variant? 'Предложение не было удалено из выборки.' : 'Объект не был удален из выборки.',
								errors: _.values(res.errors)
							});
						} else {
							window.location.reload();
						}
					}, 'json').error(function(err) {
						popupAlert.error({
							title: 'Выборка',
							text: variant? 'Предложение не было удалено из выборки.' : 'Объект не был удален из выборки.',
							errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
							errtext: $('BODY').data('admin')? err.responseText : ''
						});
					});
				}
			});
			return false;
		});
		
		// сохранение комментариев
		$('.fav-comments').bind('change', function() {
			var type = $(this).data('type');
			var req = {
				type: type,
				value: $(this).val()
			};
			if (type === 'item') req.item_id = $(this).closest('.offer-item').data('id');
			$.post('/catalog-view/saveFavoriteComment/', req, function(res) {
				if (res.errors) {
					popupAlert.error({
						title: 'Выборка',
						text: 'Ошибка при сохранении комментариев.',
						errors: _.values(res.errors)
					});
				} else {

				}
			}, 'json').error(function(err) {
				popupAlert.error({
					title: 'Выборка',
					text: 'Ошибка при сохранении комментариев.',
					errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
					errtext: $('BODY').data('admin')? err.responseText : ''
				});
			});	
		});
		
	});
});