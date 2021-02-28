define(['ui', 'editContent', 'message', 'editItemProps', 'itemVariants', 'editor'], function(ui, editContent, message, editItemProps, itemVariants, editor) {
	var itemEdit = function() {
		editItemProps();
		itemVariants();
		
		$('.item-props-edit').each(function() {
			var cont = $(this);
			if (cont.data('itemEdit-init')) return;
			cont.data('itemEdit-init', true);
			var id = $('.content-scroll-cont', cont).data('item-id');
			var typeId = $('.content-scroll-cont', cont).data('type-id');

			/* Кнопки */
			// удаление
			cont.on('click', '.action-panel-cont .action-delete', function() {
				message.confirm({
					text: 'Подтвердите удаление товара.',
					type: 'delete',
					ok: function() {
						$.post('/catalog-item/delete/', {id: id}, function(res) {
							if (res.errors) {
								message.errors(res);
							} else {
								location.href = $('.action-panel-cont .action-back').attr('href');
							}
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// изменение видимости
			cont.on('click', '.action-panel-cont .action-visibility', function() {
				var btn = $(this);
				var visible = btn.hasClass('action-show')? 0 : 1;
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						$.post('/catalog-item/changeVisible/', {
							id: id,
							entity: 'item',
							value: visible
						}, function(res) {
							if (res.errors) {
								message.errors(res);
								return;
							}
							btn.removeClass('action-show action-hide').addClass('action-' + (visible? 'show' : 'hide')).attr('title', visible? 'Отображается' : 'Не отображается');
							$('.action-icon', btn).removeClass('icon-show icon-hide').addClass('icon-' + (visible? 'show' : 'hide'));
							$('DIV', btn).text(visible? 'Отображается' : 'Не отображается');
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// копирование
			cont.on('click', '.action-panel-cont .action-copy', function() {
				var typeId = $(this).closest('.item-props-edit').find('#tabs-pages').data('type-id');
				var itemId = $(this).closest('.item-props-edit').find('#tabs-pages').data('item-id');
				editContent.open({
					getform: '/catalog-item/edit/',
					getformdata: {
						copy_item: itemId,
						type_id: typeId
					},
					getformmethod: 'post',
					getformtype: 'json',
					class: 'item-props-edit',
					loadform: function() {
						editItemProps();
						itemVariants();
						ui.initAll();
					},
					customform: true,
					success: function(res) {
						$('.view-content').html(res);
						$(window).resize();
						ui.initAll();
					}
				});
				return false;
			});

			// перемещение
			cont.on('click', '.action-move', function() {
				var itemId = $(this).closest('.item-props-edit').find('#tabs-pages').data('item-id');
				var variantId = $(this).closest('.edit_properties_form').data('variant-id');
				editContent.open({
					form: variantId? '.transfer-variant-form' : '.transfer-item-form',
					clearform: true,
					loadform: function() {
						$('H1', this).text('Перемещение ' + (variantId? 'варианта' : 'товара'));
						$('SELECT .level0', this).prop('selected', true);
						if (variantId) $('.input-variant', this).val(variantId);
						else $('.input-item', this).val(itemId);
					},
					success: function(res) {
						if (res.data && res.data.url) {
							location.href = res.data.url;
						} else {
							location.reload();
						}
					},
					errorsText: {
						'type_id:empty': 'Укажите тип для перемещения товара.',
						'type:not_final_type': 'Указан не конечный тип.',
						'item_id:not_found': 'Товар не найден.',
						'item_id:empty': 'Укажите ID товара.'
					}
				});
				return false;
			});
			$('.popup-transfer FORM').off().on('submit', function() {
				var form = $(this);
				$('.m-error', form).removeClass('m-error');
				$(this).ajaxSubmit({
					type: 'POST',
					dataType: 'json',
					success: function(res) {
						if (res.errors) {
							if (res.errors.type) {
								$('[name="type_id"]', form).addClass('m-error');
								if (res.errors.type === 'not_final_type') alert('Указан не конечный тип.');
							} else if (res.errors.type_id && res.errors.item_id) {
								$('[name="type_id"]', form).addClass('m-error');
								$('[name="item_id"]', form).addClass('m-error');
								alert('Укажите ID товара или тип, в котором будет создан новый товар.');
							} else if (res.errors.type_id) {
								$('[name="type_id"]', form).addClass('m-error');
								if (res.errors.type_id === 'empty') alert('Укажите тип для перемещения товара.');
							} else if (res.errors.item_id) {
								$('[name="item_id"]', form).addClass('m-error');
								if (res.errors.item_id === 'empty') alert('Укажите ID товара.');
							}
						} else {
							if (res.data && res.data.url) {
								location.href = res.data.url;
							} else {
								location.reload();
							}
						}
					}
				});
				return false;
			});

			/* Отзывы */
			// создание
			cont.on('click', '.reviews-page .action-add', function() {
				var btn = $(this);
				editContent.open({
					getform: '/catalog-item/edit/',
					getformmethod: 'post',
					getformtype: 'json',
					getformdata: {
						type_id: btn.closest('.reviews-page').data('type_id'),
						product_id: id
					},
					loadform: function() {
						editItemProps();
					},
					customform: true
				});
				return false;
			});
			
			// редактирование
			cont.on('click', '.reviews-list .action-edit', function() {
				var btn = $(this);
				editContent.open({
					getform: '/catalog-item/edit/',
					getformmethod: 'post',
					getformtype: 'json',
					getformdata: {
						id: btn.closest('.wblock').data('id')
					},
					loadform: function() {
						editItemProps();
					},
					customform: true
				});
				return false;
			});
			
			// удаление
			cont.on('click', '.reviews-list .action-delete', function() {
				var btn = $(this);
				message.confirm({
					text: 'Подтвердите удаление отзыва.',
					target: btn.closest('.wblock'),
					type: 'delete',
					ok: function() {
						$.post('/catalog-item/delItems/', {
							type_id: btn.closest('.reviews-page').data('type_id'),
							check: [btn.closest('.wblock').data('id')]
						}, function(res) {
							if (res.errors) {
								message.errors(res);
								return;
							}
							btn.closest('.reviews-page').html(res.content);
							$(window).resize();
							ui.initAll();
						}, 'json').error(function(err) {
							message.errors({
								text: 'Ошибка сервера: ' + err.status,
								descr: (err.status === 404 || err.status === 200)? '' : err.responseText
							});
						});
					}
				});
				return false;
			});

			/* Мета-теги */
			cont.on('click', '.seo-page .action-save', function() {
				var form = $(this).closest('FORM');
				ui.form.submit(form, {
					success: function(res) {
						message.ok('Изменения сохранены.');
						$('.seo-page', cont).html(res.content);
						$(window).resize();
						ui.initAll();
						editor();
					},
					errors: function(errors) {
						message.errors({errors: errors});
					},
					serverError: function(err) {
						message.errors({
							text: 'Ошибка сервера: ' + err.status,
							descr: (err.status === 404 || err.status === 200)? '' : err.responseText
						});
					}
				});
				return false;
			});
			
		});
	};
	
	itemEdit();
	return itemEdit;
});