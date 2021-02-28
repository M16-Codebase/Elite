define(['ui', 'editContent', 'message', 'itemEdit', 'filter'], function(ui, editContent, message, itemEdit, filter) {
	
	var initItemsList = function(el) {
		el = el? $(el) : $('FORM.items-edit');
		if (!el.length) return false;
		el.each(function() {
			var itemsForm = $(this);
			var itemsList = $('.items-list', itemsForm);
			if (itemsList.data('itemsList-init')) return;
			itemsList.data('itemsList-init', true);
						
			//создать
			$('#items, .child-page').on('click', '.actions-panel .action-add', function() {
				var req = {};
				req.type_id = $(this).closest('.tab-page').data('type-id');
				if ($(this).closest('.tab-page').data('parent-id')) {
					req.parent_id = $(this).closest('.tab-page').data('parent-id');
				}
				editContent.open({
					getform: '/catalog-item/edit/',
					getformdata: req,
					getformmethod: 'post',
					getformtype: 'json',
					class: 'item-props-edit',
					loadform: function() {
						initItemsList();
						itemEdit();
						ui.initAll();
					},
					customform: true,
					success: function(res) {
						res = filter.replace(res);
						$('.view-content').html(res);
						$(window).resize();
						filter();
						itemEdit();
						ui.initAll();
					}
				});
				return false;
			});
			
			// редактировать
			itemsForm.on('click', '.action-edit', function() {
				var id = $(this).closest('.wblock').data('item_id');
				editContent.open({
					getform: '/catalog-item/edit/',
					getformdata: {id: id},
					getformmethod: 'post',
					getformtype: 'json',
					class: 'item-props-edit',
					loadform: function() {
						initItemsList();
						itemEdit();
						ui.initAll();
					},
					customform: true,
					success: function(res) {
						res = filter.replace(res);
						$('.view-content').html(res);
						$(window).resize();
						filter();
						itemEdit();
						ui.initAll();
					}
				});
				return false;
			});

			// изменение видимости товара
			itemsForm.on('click', '.action-visibility', function() {
				if ($(this).closest('.unchangeable').length) return false;
				if (!$(this).hasClass('m-active')) return false;
				var btn = $(this);
				var item = btn.closest('.wblock');
				var visible = btn.hasClass('action-show')? 0 : 1;
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						$.post('/catalog-item/changeVisible/', {
							id: $('INPUT[name=item_id]', item).val(),
							value: visible,
							entity: 'item'
						}, function(res) {
							if (res.errors) {
								message.errors({
									errors: res.errors,
									errorsTextHandler: function(err) {
										if (err.error.match(/В данном айтеме нет вариантов, поэтому его нельзя показать/gi)) {
											var row = $('.wblock[data-item_id="' + err.key + '"]', itemsForm);
											var title = $('.item-title', row).text();
											var itemText = row.data('item-text').toLowerCase() || 'товаре';
											var varText = row.data('variant-text').toLowerCase() || 'вариантов';
											return 'В ' + itemText + ' «' + title + '» (ID ' + err.key + ') нет ' + varText + ', поэтому его нельзя показать.';
										}
									}
								});
								return;
							};
							btn.removeClass('action-show action-hide').addClass(visible? 'action-show' : 'action-hide').attr('title', visible? 'Отображается' : 'Не отображается');
							$('I', btn).removeClass('icon-show icon-hide').addClass(visible? 'icon-show' : 'icon-hide');
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// показать выбранные товары
			$('#items, .child-page').on('click', '.actions-panel .action-show', function() {
				if ($(this).hasClass('m-inactive')) return false;
				var page = $('INPUT[name=page]', itemsForm).val();
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						ui.form.submit(itemsForm, {
							url: '/catalog-item/changeVisible/' + ((page && page > 1)? '?page='+page : ''),
							method: 'POST',
							data: {
								entity: 'item',
								value: 1,
								getList: 1
							},
							success: function(res) {
								res.content = filter.replace(res.content);
								itemsList.html(res.content);
								$(window).resize();
								ui.initAll();
								filter();
							},
							errors: function(errors, res) {
								if (res.content) {
									res.content = filter.replace(res.content);
									itemsList.html(res.content);
									$(window).resize();
									ui.initAll();
									filter();
								}
								message.errors({
									errors: errors,
									errorsTextHandler: function(err) {
										if (err.error.match(/В данном айтеме нет вариантов, поэтому его нельзя показать/gi)) {
											var row = $('.wblock[data-item_id="' + err.key + '"]', itemsForm);
											var title = $('.item-title', row).text();
											var itemText = row.data('item-text').toLowerCase() || 'товаре';
											var varText = row.data('variant-text').toLowerCase() || 'вариантов';
											return 'В ' + itemText + ' «' + title + '» (ID ' + err.key + ') нет ' + varText + ', поэтому его нельзя показать.';
										}
									}
								});
							},
							serverError: function(err) {
								message.serverErrors(err);
							}
						});
					}
				});
				return false;
			});

			// скрыть выбранные товары
			$('#items, .child-page').on('click', '.actions-panel .action-hide', function() {
				if ($(this).hasClass('m-inactive')) return false;
				var page = $('INPUT[name=page]', itemsForm).val();
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						ui.form.submit(itemsForm, {
							url: '/catalog-item/changeVisible/' + ((page && page > 1)? '?page='+page : ''),
							method: 'POST',
							data: {
								entity: 'item',
								value: 0,
								getList: 1
							},
							success: function(res) {
								res.content = filter.replace(res.content);
								itemsList.html(res.content);
								$(window).resize();
								ui.initAll();
								filter();
							},
							errors: function(errors) {
								message.errors({errors: errors});
							},
							serverError: function(err) {
								message.serverErrors(err);
							}
						});
					}
				});
				return false;
			});	

			// удалить выбранные товары
			$('#items, .child-page').on('click', '.actions-panel .action-delete', function() {
				if ($(this).hasClass('m-inactive')) return false;
				var page = $('INPUT[name=page]', itemsForm).val();
				message.confirm({
					text: 'Подтвердите удаление выбранных позиций.',
					type: 'delete',
					ok: function() {
						ui.form.submit(itemsForm, {
							url: '/catalog-item/delItems/' + ((page && page > 1)? '?page='+page : ''),
							method: 'POST',
							success: function(res) {
								res.content = filter.replace(res.content);
								itemsList.html(res.content);
								$(window).resize();
								ui.initAll();
								filter();
							},
							errors: function(errors) {
								message.errors({errors: errors});
							},
							serverError: function(err) {
								message.serverErrors(err);
							}
						});
					}
				});
				return false;
			});
			
			// удалить один товар
			itemsForm.on('click', '.action-delete', function() {
				if ($(this).hasClass('m-inactive')) return false;
				var btn = $(this);
				var page = $('INPUT[name=page]', itemsForm).val();
				message.confirm({
					text: 'Подтвердите удаление позиции.',
					target: btn.closest('.wblock'),
					type: 'delete',
					ok: function() {
						$('.check-item', itemsForm).prop('checked', false);
						btn.closest('.wblock').find('.check-item').prop('checked', true);
						ui.form.submit(itemsForm, {
							url: '/catalog-item/delItems/' + ((page && page > 1)? '?page='+page : ''),
							method: 'POST',
							success: function(res) {
								res.content = filter.replace(res.content);
								itemsList.html(res.content);
								$(window).resize();
								ui.initAll();
								filter();
							},
							errors: function(errors) {
								message.errors({errors: errors});
							},
							serverError: function(err) {
								message.serverErrors(err);
							}
						});
					}
				});
				return false;
			});

			// import
			var importPreloader = $('.popup-import .popup-preloader');
			var importSended = $('.popup-import .popup-sended');
			var importChanged = false;
			$('#items, .child-page').on('click' ,'.actions-panel .action-import', function() {
				var currentId = $('.select-type SELECT[name=type_id]').val();
				$('.popup-import SELECT[name="type_id"] OPTION[value="' + currentId + '"]').attr('selected', true);
				$('.popup-import SELECT[name="type_id"]').trigger('liszt:updated');
				importPreloader.hide();
				importSended.hide();
				importChanged = false;
				$('.popup-import').dialog({
					title: 'Импорт товаров'
				}).dialog('open').dialog({
					close: function() {
						if (importChanged) location.reload();
					}
				});
			});
			$('.close-sended', importSended).click(function() {
				$('.popup-import FORM INPUT[name="file"]').val('');
				importSended.fadeOut(300);
			});
			$('A', importSended).click(function() {
				location.href = $(this).attr('href');
			});
			$('.popup-import FORM').ajaxForm({
				dataType: 'json',
				beforeSubmit: function() {
					importPreloader.fadeIn(300);
				},
				success: function(res) {
					if (!res.status) {
						if (res.error) {
							message.errors(res);
						} else {
							message.errors('Ошибка импорта.');
						}
						importPreloader.hide();
					}
					else {
						importSended.show();
						importPreloader.fadeOut(300);
						importChanged = true;
					};
				},
				error: function(err) {
					importPreloader.fadeOut(300);
					message.serverErrors(err);
				}
			});
			$('.popup-import .download_keys').click(function(){
				$(this).attr('href', '/exchange/downloadExample/');
				$(this).attr('href', $(this).attr('href') + '?type_id=' + $('.popup-import SELECT[name="type_id"]').val());
			});

			$('.popup-import .request_file').click(function(){
				$.get($(this).attr('href'), {type_id: $('.popup-import SELECT[name="type_id"]').val()}, function(result){
					alert(result);
				});
				return false;
			});
			
			// получить ids
			var idsPopup = $('.popup-ids');
			var itemIdsTextarea = $('TEXTAREA.item-ids', idsPopup);
			var varIdsTextarea = $('TEXTAREA.var-ids', idsPopup);		
			$('.get-ids').click(function() {
	//			editContent.open({
	//				form: '.get-ids-form',
	//				clearform: true,
	//				success: function(res) {
	//					if (res.data && res.data.url) {
	//						location.href = res.data.url;
	//					}
	//				}
	//			});
	//			$('.aside-filter .items-filter').ajaxSubmit({
	//				url: '/catalog-item/getIds/',
	//				dataType: 'json',
	//				type: 'GET',
	//				data: {entity: 'items'},
	//				success: function(res) {
	//					var ids = _.values(res).join(',');
	//					itemIdsTextarea.text(ids);
	//					$('.aside-filter .items-filter').ajaxSubmit({
	//						url: '/catalog-item/getIds/',
	//						dataType: 'json',
	//						type: 'GET',
	//						data: {entity: 'variants'},
	//						success: function(res) {
	//							var ids = _.values(res).join(',');
	//							varIdsTextarea.text(ids);
	//							idsPopup.dialog('open');
	//						},
	//						error: function(err) {
	//							alert('Ошибка сервера: ' + err.status);
	//						}
	//					});	
	//				},
	//				error: function(err) {
	//					alert('Ошибка сервера: ' + err.status);
	//				}
	//			});
	//			return false;
			});

			// изменение свойств		
			(function() {
				var form = $('.edit-single-prop-form');
				var propsList = $('.properties', form);
				var preloader = $('.popup-preloader', form);
				var origin = $('.prop-item:first', propsList).clone();

				$('.current-form', form).empty();
				$('.aside-filter .user-form .field').clone().appendTo($('.current-form', form));
				$('.current-form INPUT, .current-form SELECT', form).each(function() {
					if (!$(this).val()) $(this).remove();
				});
				$('.slider-wrap', form).each(function() {
					var slider = $('.slider', this);
					if ($('.input-min', this).val() <= slider.data('min')) $('.input-min', this).remove();
					if ($('.input-max', this).val() >= slider.data('max')) $('.input-max', this).remove();
				});

				$('.actions-panel .action-edit-group').click(function() {
					var cont = $(this).closest('.actions-cont');
					// выбранные айтемы
					var selected = $('.items-list .check-item:checked', cont);
					// кол-во выбранных айтемов
					var count = selected.length;
					// ничего не выбрано, то выбрать все
					if (!count) {
						count = $('.items-list', cont).data('count') + ' (все найденные)';
					}
					// очищаем попап от выбранных айтемов
					$('.selected-items', form).empty();
					// добавляем чекбоксы выбранных элементов в .selected-items
					selected.each(function() {
						$('.selected-items', form).append($(this).clone());
					});	
					// вставляем количество выбранных элементов
					$('.count', form).html(count);
					// очищаем и добавляем первую строку
					propsList.empty().append(origin.clone());
					preloader.hide();
					editContent.open({
						form: '.edit-single-prop-form',
						success: function(res) {
							if (res.data && res.data.url) {
								location.href = res.data.url;
							}
						}
					});
					return false;
				});

				form.on('change', '.sel-prop', function() {
					var select = $(this);
					if (select.val()) {
						$.post('/catalog-item/editPropertyFace/', {id: select.val()}, function(res) {
							var valueBlock = select.closest('.wblock').find('.block-value');
							var val = $('.new-val', valueBlock).is('.flag-vals, SELECT')? '' : $('.new-val', valueBlock).val();
							valueBlock.html(res);
							if (val) {
								if ($('.new-val', valueBlock).is('SELECT')) {
									$('.new-val OPTION[value="' + val + '"]', valueBlock).attr('selected');
								} else {
									$('.new-val', valueBlock).val(val);
								}
							}
							$('.new-val', valueBlock).prop('name', 'values[' + select.val() + ']');
							select.prop('name', 'props[' + select.val() + ']');
							ui.initAll();
						});
					}
				});

				// добавление свойства
				propsList.on('click', '.action-add', function() {
					if (!$(this).closest('.prop-item').find('.sel-prop').val()) {
						return false;
					}
					$(this).removeClass('action-add').addClass('action-delete').attr('title', 'Удалить');
					$('I', this).removeClass('icon-add').addClass('icon-delete');
					$(this).closest('.properties').append(origin.clone().css({opacity: 0}).animate({opacity: 1}, 400));
					return false;
				});

				// удаление свойства
				propsList.on('click', '.action-delete', function() {
					var btn = $(this);
					message.confirm({
						text: 'Подтвердите удаление свойства.',
						type: 'delete',
						ok: function() {
							btn.closest('.prop-item').fadeOut(function() {
								$(this).remove();
							});
						}
					});
					return false;
				});

				///catalog-item/changeFilteredItemsProp/
	//			$('.save').on('click', function(){
	//				ui.form.submit(form, {
	//					url: 'fsdf',
	//					method: 'POST',
	//					datatype: 'json',
	//					success: function(res) {
	//						alert('suc');
	//					},
	//					errors: function () {
	//						alert('err');
	//						// TODO
	//					}
	//				});
	//			})

	//			form.ajaxForm({
	//				dataType: 'json',
	//				beforeSubmit: function() {
	//					preloader.fadeIn(300);
	//				},
	//				success: function(res) {
	//					if (res.status) {
	//						form.dialog('close');
	//						saveIcon();
	//					} else {
	//						//errors(res);
	//					}
	//					preloader.fadeOut(300);
	//				},
	//				error: function() {
	//					preloader.fadeOut(300);
	//					alert('Произошла ошибка на сервере.');
	//				}
	//			});
			})();
		});
	};
	
	initItemsList();
	return initItemsList;
});