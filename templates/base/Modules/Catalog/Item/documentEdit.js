$(function(){
	require(['ui', 'tabs', 'picUploader', 'editor', 'editItemProps', 'popupAlert', 'objectComments', 'itemVariants'], function(ui, tabs, picUploader, editor, editItemProps, popupAlert) {
		tabs();	
		var itemId = $('#tabs-pages').data('item-id');
		
		// curator popup
		$('.curator-cont').each(function() {
			var cont = $(this);
			var popup = $('.curator-popup', cont);
			var timer = 0;
			$(cont).mouseenter(function(){
				clearTimeout(timer);
				timer = setTimeout(function() {
					popup.stop().fadeIn('slow');
				}, 400);				
				return false;
			}).mouseleave(function(){
				clearTimeout(timer);
				popup.stop().fadeOut('fast');
				return false;
			});
			$('.close-popup', popup).click(function(){
				popup.stop().fadeOut('fast');
				clearTimeout(timer);
			});
		});		
		$('.actions-panel .action-visability A').click(function() {
			return false;
		});
		
		/* КНОПКИ */
		// загрузить флаер
		$('.action-panel-cont .action-flyer_up').click(function() {
			var fileId = $(this).attr('data-file-ids');
			var propId = $(this).data('prop-id');
			var id = $(this).data('id');
			if (fileId) {
				fileId = fileId.split('|');
			} else return false;
			var segCount = fileId.length;
			var segI = 0;
			var initForm = function(form) {
				$('.id-input', form).val(id);
				$('.prop-id-input', form).val(propId);
				$('.popup-flyer').append(form);
				// загрузка
				form.submit(function() {
					if (form.hasClass('sending')) return false;
					if (!$('INPUT:file', form).val()) {
						popupAlert.ok({
							title: 'Ошибка',
							text: 'Не выбран файл для загрузки.'
						});
						return false;
					}
					var segment = form.data('segment');
					form.addClass('sending');
					form.ajaxSubmit({
						dataType: 'json',
						success: function(res) {
							if (res.errors) {
								form.removeClass('sending');
								popupAlert.error({
									text: 'Не удалось загрузить флаер.',
									errors: _.values(res.errors)
								});
							} else {
								form.addClass('sended').removeClass('sending');
								$('.file-id-input', form).val(res.data.file_id);
								$('INPUT:file', form).val('');
								$('.delete', form).removeClass('a-hidden');
								var ids = $('.action-panel-cont .action-flyer_up').attr('data-file-ids');
								ids = ids.replace(segment, '#');
								ids = ids.replace(/#\:\d+/i, segment + ':' + res.data.file_id);
								$('.action-panel-cont .action-flyer_up').attr('data-file-ids', ids);
							}
						},
						error: function(err) {
							form.removeClass('sending');
							popupAlert.error({
								text: 'Не удалось загрузить флаер.',
								errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
								errtext: $('BODY').data('admin')? err.responseText : ''
							});
						}
					});
					return false;
				});
				// удаление
				$('.delete', form).click(function() {
					if (form.hasClass('sending')) return false;
					if ($(this).hasClass('a-hidden')) return false;
					var segment = form.data('segment');
					popupAlert.confirm({
						title: 'Удаление',
						text: 'Вы уверены, что хотите удалить файл?',
						okText: 'Удалить',
						ok: function() {
							form.addClass('sending');
							form.ajaxSubmit({
								url: '/catalog-item/deleteFileDataType/',
								dataType: 'json',
								success: function(res) {
									if (res.errors) {
										form.removeClass('sending');
										popupAlert.error({
											text: 'Не удалось удалить флаер.',
											errors: _.values(res.errors)
										});
									} else {
										form.removeClass('sending').addClass('deleted');
										$('.file-id-input', form).val(0);
										$('INPUT:file', form).val('');
										$('.delete', form).addClass('a-hidden');
										$('.td-title', form).text($('.td-title', form).text());
										var ids = $('.action-panel-cont .action-flyer_up').attr('data-file-ids');
										ids = ids.replace(segment, '#');
										ids = ids.replace(/#\:\d+/i, segment + ':' + 0);
										$('.action-panel-cont .action-flyer_up').attr('data-file-ids', ids);
									}
								},
								error: function(err) {
									form.removeClass('sending');
									popupAlert.error({
										text: 'Не удалось удалить флаер.',
										errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
										errtext: $('BODY').data('admin')? err.responseText : ''
									});
								}		
							});
						}
					});
					return false;
				});
				// загрузить новый
				$('.success .close', form).click(function() {
					form.removeClass('sended deleted');
					return false;
				});
			};
			$('.popup-flyer').empty();
			for (var i in fileId) {
				fileId[i] = fileId[i].split(':');
				$.post('/catalog-item/fileDataType/?segment_title=' + fileId[i][1], {
					segment_id: fileId[i][0],
					file_id: fileId[i][2]
				}, function(res) {
					if (res.errors) {
						popupAlert.error({
							text: 'Не удалось загрузить флаер.',
							errors: _.values(res.errors)
						});
					} else {	
						initForm($(res.content));
						ui.initAll();
						if (++segI === segCount) $('.popup-flyer').dialog('open');
					}
				}, 'json').error(function(err) {
					popupAlert.error({
						text: 'Не удалось загрузить флаер.',
						errors: $('BODY').data('admin')? 'Ошибка сервера: ' + err.status : 'Ошибка сервера',
						errtext: $('BODY').data('admin')? err.responseText : ''
					});
				});
			}
			return false;
		});
		
		// удаляем
		$('.action-panel-cont .action-delete A').click(function() {
			if (confirm('Удалить товар?')) {
				window.location = '/catalog-item/delete/?id=' + itemId;
			}
			return false;
		});
		
		// показываем, прячем
		$('.action-panel-cont .action-visability .dropdown-menu LI').click(function() {
			if ($(this).closest('.action-button').hasClass('m-inactive')) return false;
			var visible = $(this).data('visible');			
			popupAlert.confirm({
				title: 'Изменение видимости',
				text: 'Вы уверены, что хотите ' + (visible? 'показать' : 'скрыть') + ' объект?',
				okText: (visible? 'Показать' : 'Скрыть'),
				ok: function() {					
					$.post('/catalog-item/changeItemProp/', {
						id: itemId,
						key: 'visible',
						value: visible
					}, function(res) {
						if (res.errors) return false;
						location.reload();
					}, 'json');
				}
			});
			return false;
		});
				
		
		/* ХАРАКТЕРИСТИКИ */	
        				
		/* ОПИСАНИЕ */
		// языки
		$('.post-lang-select').change(function() {
			var lang = $(this).val();
			$(this).closest('.tab-page').find('.item-post-form').addClass('a-hidden').filter('.post-lang-' + lang).removeClass('a-hidden');
			$(window).scroll();
		});
		// загрузка статей
		var postI = 0;
		$('.item-post-form').each(function() {
			var form = $(this);
			var req = {
				id: form.data('id'),
				post_id: form.data('post-id'),
				segment_id: form.data('segment-id'),
				property_id: form.data('property-id')
			};
			$.post('/catalog-item/postDataType/', req, function(res) {
				picUploader($('.img-uploader-gallery'));
				form.html(res.content);
				editItemProps();
				ui.initAll();
				postI++;
				if (postI === $('.item-post-form').length) editor(/(postEditor)/);
			}, 'json');
		});
		// смена статуса
		$('#description').delegate('.action-visability .dropdown-menu LI', 'click', function() {
			var status = $(this).data('status');
			var icon = $(this).closest('.action-visability');
			var input = $('.status-input', icon);
			var text = $('.dropdown-toggle', icon);
			$(this).addClass('a-hidden').siblings().removeClass('a-hidden');
			ui.dropdown.close($(this).closest('.dropdown'));
			input.val(status);
			icon.removeClass('action-hide action-show');
			if (status === 'close') {
				icon.addClass('action-show');
				text.text('Показан');
			} else {
				icon.addClass('action-hide');
				if (status === 'new') text.text('Скрыт');
				else text.text('Удален');
			}
		});
		// сохраняем описание		
		$('#description').delegate('.actions-panel .action-save', 'click', function() {
			var form = $('#description .edit_post_form:not(.a-hidden)');			
			form.ajaxSubmit({
				url: "/catalog-item/editPostDataType/",				
				success: function() {
					if ($('TEXTAREA[name="text"]', form).text()) {
						$('#tabs A[data-tab="description"] .num').text('+');
					} else {
						$('#tabs A[data-tab="description"] .num').text('-');
					}
				}
			});
			return false;
		});
		
		
		/* ФОТО */		
		// счётчик фото
		var setPhotoCount = function() {
			var galNum = $('#tabs .photo-count');
			var schNum = $('#tabs .scheme-count');
			galNum.text($('#photo .uploaded-image').length);
			schNum.text($('#scheme .uploaded-image').length);
		};
		if ($('#photo .gallery-block').length && $('#scheme .gallery-block').length) {
			setInterval(setPhotoCount, 2000);
		}	
		
		
		// АКЦИИ
		var setPromoCount = function() {
			var num = $('#tabs .discount-count');
			num.text($('#discount .discount-block TBODY .delete').length);
		};
		// сохраняем
		$('#discount').delegate('.actions-panel .action-save', 'click', function() {
			$('#discount .edit_properties_form').submit();
			return false;
		});
		
	});
});