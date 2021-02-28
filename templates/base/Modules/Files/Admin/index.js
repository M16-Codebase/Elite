$(function() {
	require(['ui'], function(ui) {
		
		// выбор типа
		$('.select-file-type SELECT').change(function() {
			var id = $(this).val();
			if (id) {
				$(this).closest('FORM').submit();
			}
		});

		
		// отправка форм
		$('.files-popup FORM').ajaxForm({
			resetForm: true,
			beforeSubmit: function(data, form) {
				$('.submit', form).addClass('m-inactive');
			},
			success: function(res) {
				$('.files_list').html(res);
				ui.initAll();
				$('.popup-window').dialog('close');
				$('.popup-errors').dialog({
					close: function(e, ui) {
						$(ui).dialog( "destroy" );
					}
				}).dialog('open');
			},
			complete: function() {
				$('.files-popup .submit').removeClass('m-inactive');
			}
		});
		
		// список организаций
		var initOrgScripts = function() {
			$('.ords-list').each(function() {
				var enumList = $(this);
				var enumListProps = $('.org-values', enumList);
				var enumListOrigin = $('.origin', enumList);
				var enumListAdd = $('.add-value', enumList);			
				var addToEnum = function() {
					if (!$('INPUT', enumListAdd).val()) return false;
					var name = $('INPUT', enumListAdd).val();
					var origin = enumListOrigin.clone();
					$('INPUT', origin).val(name).attr('name', 'orgs[]');
					ui.autocomplete.init($('INPUT', origin));
					enumListProps.append(origin.removeClass('origin'));
					$('INPUT', enumListAdd).val('').blur();					
					return false;
				};
				$('.add', enumListAdd).click(addToEnum);
				$('INPUT', enumListAdd).keydown(function(e) {
					if (e.keyCode === 13) {
						addToEnum();
						return false;
					}		
				});
				enumListProps.delegate('.delete', 'click', function() {
					$(this).closest('.org-item').fadeOut(function() {
						$(this).remove();
					});
				});
			});
		};
		initOrgScripts();
		
		// номера товаров
		var initNemArea = function() {
			$('.num-area').each(function() {
				var cont = $(this);
				var input = cont.next('INPUT');
				var nNumReg = /\d{3}-?\d{3}-?\d{6}/g;
				var saveNum = function() {
					var nums = _.map(cont.tagit('tags'), function(tag) {
						return $(tag.element).attr('tagvalue');
					});
					input.val(nums.join());
				};
				var setError = function(el, text) {
					var timer = 0;
					$(el).addClass('m-error').attr('title', text);
					timer = setTimeout(function() {
						$(el).fadeOut(function() {
							$('.tagit-close', el).click();
						});
					}, 3000);
					$('.tagit-close', el).click(function() {
						clearTimeout(timer);
					});
				};
				var preAdded = {};
				$('LI.tagit-choice', cont).each(function() {
					preAdded[$(this).attr('tagvalue')] = $(this).attr('title');
				});
				cont.tagit({
					highlightOnExistColor: '#ea3131',
					beforeAdded: function(code, value) {
						if (value) return {};
						var match = code.match(nNumReg);
						if (match) {
							code = match[0].replace(/-/g, '');							
							code = code.substr(0,3) + '-' + code.substr(3,3) + '-' + code.substr(6,6);
						}
						return {
							value: code,
							label: code
						};
					},
					tagsChanged: function(code, action, el) {
						var match = code.match(nNumReg);
						if (match) {
							switch(action) {
								case 'added':
									$.post('/catalog-item/checkIssetVariantByCode/', {code: code}, function(res) {
										if (res.error) {
											setError(el, 'Товар не найден');								
										} else {
											$(el).attr('title', res.title).attr('tagvalue', res.id);
											saveNum();
										}
									}, 'json').fail(function() {
										setError(el, 'Ошибка сервера');
									});
									break;
								case 'popped':
									saveNum();
									break;
							}
						} else {
							setError(el, 'Неверный формат');
						}						
					}
				});
				for (var id in preAdded) {
					$('.tagit-choice[tagvalue="' + id + '"]', cont).attr('title', preAdded[id]);
				}
				saveNum();
			});
		};
		initNemArea();
		
		// новый файл
		$('.actions-panel .action-add').click(function() {
			$('.popup-upload-file .org-item:not(.origin)').remove();
			$('.popup-upload-file .num-area .tagit-choice').each(function() {
				$('.tagit-close', this).click();
			});
			$('.popup-upload-file FORM').clearForm();
			$('.popup-upload-file .chosen .default').attr('selected', 'selected');
			$('.popup-upload-file .chosen').trigger('liszt:updated');
			$('.popup-upload-file').dialog({
				title: 'Загрузка файла'				
			}).dialog('open');
			return false;
		});		
		
		// заменить файл
		$('.files_list').delegate('.reload', 'click', function() {
            $.post('/files-edit/editFields/', {id: $(this).closest('TR').data('id')}, function(result){
                $('.popup-reload-file FORM').html(result);
				ui.initAll();
				initNemArea();
				initOrgScripts();
                $('.popup-reload-file').dialog({
                    title: 'Свойства файла'
                }).dialog('open');
            });
			return false;
		});
				
		// форма удаления
		$('.files_list').ajaxForm({
			resetForm: true,
			success: function(responseHTML) {
				$('.files_list').html(responseHTML);
				ui.initAll();
				$('.popup-errors').dialog({
					close: function(e, ui) {
						$(ui).dialog( "destroy" );
					}
				}).dialog('open');
			}
		}); 
		
		// удалить выбранные файлы
		$('.actions-panel .action-delete').click(function() {
			if (confirm('Удалить выбранные файлы?')) {
				$('.files_list').submit();
			}
			return false;
		});
    
		// удалить файл
		$('.files_list').delegate('.delete', 'click', function() {
			if (confirm('Удалить файл?')) {
				var tr = $(this).closest('TR');
				$('.files_list INPUT.check-item').removeAttr('checked');
				$('INPUT.check-item', tr).attr('checked', 'checked');
				$('.files_list').submit();
			}
			return false;
		});
		
		// список вариантов
		$('.files_list').delegate('.file-variants', 'click', function() {
			var tr = $(this).closest('TR');
			$.post('/files-edit/fileVariants/', {id: tr.data('id')}, function(res) {
				$('.popup-file-variants').html(res).dialog('open');
			});
			return false;
		});
		
	});
});