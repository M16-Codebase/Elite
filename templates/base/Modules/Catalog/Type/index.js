$(function() {
	require(['ui', 'editContent', 'message', 'editProperty', 'itemsList', 'picUploader', 'editor', 'banners'], function(ui, editContent, message, editProperty) {
		var typeId = $('#tabs-pages').data('type-id');

		// параметры сайта
		$('.config-submit').click(function() {
			$('#items .edit_properties_form').submit();
			return false;
		});

		/* ТИПЫ */
		(function() {
			var typeForm = $('#types .type-form');
			var typesList = $('.bush-catalog', typeForm).length? $('.bush-catalog', typeForm) : $('.types-list .white-body', typeForm);
			
			// создание типа
			$('#types .actions-panel, .types-list').on('click', '.action-add', function() {
				if ($(this).data('nest')) {
					var formdata = {nested_in: $(this).data('nest')};
				} else {
					var formdata = {};
				}
				editContent.open({
					getform: '/catalog-type/editPopup/',
					getformdata: {
						parent_id: $('INPUT[name="parent_id"]', typeForm).val()
					},
					formdata: formdata,
					method: 'get',
					success: function(res) {
						typesList.html(res.content);
						editContent.close();
						$(window).resize();
						ui.initAll();
						$('.tabs .count-types').text($('.wblock', typesList).length - 1);
					}
				});
				return false;
			});
			
			// удаление типа
			$('.types-list').on('click', '.action-delete', function() {
				var cont = $(this).closest('.wblock');
				var parent = cont.find('[name="parent_id"]').val();
				var id = cont.find('[name="type_id"]').val();
				var check = {};
				check[id] = 1;
				message.confirm({
					text: 'Подтвердите удаление каталога',
					type: 'delete',
					ok: function() {
						$.post('/catalog-type/delete/', {
							parent_id: parent,
							check: check
						}, function(res) {
							if (res.errors) {
								message.errors(res);
								return;
							}
							cont.closest('.types-list').html(res.content);
							$(window).resize();
							ui.initAll();
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// слайдбокс для падежей
			$('.main-content-inner').on('click', '.allow-variants', function(){
				var slideEl = $(this).closest('.wblock');
				ui.slidebox(slideEl,{
					body: '.white-inner-cont',
					open: function() {$(window).resize();},
					close: function() {$(window).resize();}
				});
				if ($(this).prop('checked')) {
					ui.slidebox.open(slideEl);
				} else {
					ui.slidebox.close(slideEl);
				}
			});

			// Получение падежей
			$('.main-content-inner').on('click', '.action-button.apply-object', function(){
				var sendValue = $(this).closest('.white-block-row').find('input[name=send_name]').val();
				var type = $(this).data('type');
				var block = $(this).closest('.wblock');
				$.post('/catalog-type/getWordCases/', {
					word: sendValue
				}, function(res) {
					if (res.errors) {
						message.errors(res);
						return;
					}
					var word_cases = res.data.result;
					for ( var resId in word_cases ) {
						block.find('input[name="word_cases['+type+'][1]['+word_cases[resId].case+']"]').val(word_cases[resId]['1'].toLowerCase());
						block.find('input[name="word_cases['+type+'][2]['+word_cases[resId].case+']"]').val(word_cases[resId]['2'].toLowerCase());
					}
				}, 'json').error(function(err) {
					message.serverErrors(err);
				});
			});

			// отмена возможности создавать свойства
			$('.main-content-inner').on('change', '.items-to-properties', function() {
				if ($(this).is(':checked')) return;
				var input = $(this);
				var text = input.data('text');
				message.confirm({
					text: 'При данном изменении каталога удалятся все свойства и значения свойств-' + text + '.',
					target: input.closest('.wblock'),
					cancel: function() {
						input.prop('checked', true);
					}
				});
			});

			// редактирование типа
			typesList.on('click', '.edit-type', function() {
				var type = $(this).closest('.wblock');
				if (!$(this).hasClass('m-active')) return false;
				editContent.open({
					getform: '/catalog-type/editPopup/',
					getformdata: {
						id: $('INPUT[name=type_id]', type).val(),
						parent_id: $('INPUT[name=parent_id]', typeForm).val()
					},
					success: function(res) {
						typesList.html(res.content);
						editContent.close();
					}
				});
				return false;
			});

			// удаление выбранных типов
			$('#types').on('click', '.actions-panel .action-delete', function() {
				if ($(this).hasClass('m-inactive')) return false;
				message.confirm({
					text: 'Подтвердите удаление выбранных типов.',
					type: 'delete',
					ok: function() {
						ui.form.submit(typeForm, {
							url: '/catalog-type/delete/',
							success: function(res) {
								typesList.html(res.content);
								$('.tabs .count-types').text($('.wblock', typesList).length - 1);
							},
							errors: function (errors) {
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

			// показать выбранные типы
			$('#types').on('click', '.actions-panel .more-link-show', function() {
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						ui.form.submit(typeForm, {
							url: '/catalog-type/setTypesVisible/',
							success: function(res) {
								typesList.html(res.content);
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

			// скрыть выбранные типы
			$('#types').on('click', '.actions-panel .more-link-hide', function() {
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						ui.form.submit(typeForm, {
							url: '/catalog-type/setTypesHidden/',
							success: function(res) {
								typesList.html(res.content);
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

			// изменение видимости типа
			typesList.on('click', '.action-visibility', function() {
				if ($(this).closest('.unchangeable').length) return false;
				if (!$(this).hasClass('m-active')) return false;
				var hidden = $(this).hasClass('action-show');
				var type = $(this).closest('.wblock');
				message.confirm({
					text: 'Подтвердите изменение видимости.',
					ok: function() {
						$.post('/catalog-type/updateHidden/', {
							id: $('INPUT[name=type_id]', type).val(),
							parent_id: $('INPUT[name=parent_id]', typeForm).val(),
							hidden: hidden? 1 : 0
						}, function(res) {
							if (res.errors) {
								message.errors(res);
							} else {
								typesList.html(res.content);
							}
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// фиксировать тип
			typeForm.on('change', '.fix-type', function() {
				var fixed = this.checked? 1 : 0;
				var type = $(this).closest('.wblock');
				$.post('/catalog-superadmin/updateTypeFixed/', {
					type_id: $('INPUT[name=type_id]', type).val(),
					fixed: fixed
				});
			});

		}());
		
		
		/* ПРАВИЛА */
		(function() {
			var rulesList = $('#rules .rules-list');
			var editRules = function() {
				var form = $(this);
				form.on('click', '.rules-data .add-btn', function() {
					var cont = $(this).closest('.rules-data');
					var clone = $('.origin', cont).clone().removeClass('origin a-hidden').hide();
					$('.add-row', cont).before(clone);
					clone.slideDown(300, function() {
						$(window).resize();
					});
					return false;
				});
				form.on('click', '.rules-data .action-delete', function() {
					var row = $(this).closest('.white-block-row');
					if (!row.siblings('.white-block-row:not(.origin)').length) return false;
					message.confirm({
						text: 'Подтвердите удаление правила.',
						type: 'delete',
						ok: function() {
							row.stop().slideUp(300, function() {
								$(this).remove();
								$(window).resize();
							});
						}
					});
					return false;
				});
				var getTypeIds = function() {
					var typeIds = [];
					$('.rules-data .type-select', form).each(function() {
						if ($(this).val()) typeIds.push($(this).val());
					});
					return typeIds;
				};
				form.on('change', '.rules-data .type-select', function() {
					$.post('/catalog-type/dynamicRuleProps/', {
						rule_type_ids: getTypeIds(),
						type_id: typeId
					}, function(res) {
						if (res.errors) {
							message.errors(res);
							return false;
						}
						$('.rules-data .prop-select', form).each(function() {
							var val = $(this).val();
							$(this).html(res.content).val(val);
							if (!$(this).val()) {
								$(this).val('').closest('.white-block-row').find('.rule-prop-opts').html('');
							}
						});
					}, 'json').error(function(err) {
						message.errors({
							text: 'Ошибка сервера: ' + err.status,
							descr: (err.status === 404 || err.status === 200)? '' : err.responseText
						});
					});
					return false;
				});
				form.on('change', '.rules-data .prop-select', function() {
					var val = $(this).val();
					var row = $(this).closest('.white-block-row');
					$.post('/catalog-type/dynamicRulePropFields/', {
						rule_type_ids: getTypeIds(),
						type_id: typeId,
						prop_key: val
					}, function(res) {
						if (res.errors) {
							message.errors(res);
							return false;
						}
						$('.rule-prop-opts', row).html(res.content);
						$(window).resize();
					}, 'json').error(function(err) {
						message.errors({
							text: 'Ошибка сервера: ' + err.status,
							descr: (err.status === 404 || err.status === 200)? '' : err.responseText
						});
					});
					return false;
				});
				form.on('submit', function() {
					var rule = {};
					var types = [];
					$('.rule-types .type-select', form).each(function() {
						if (!$(this).val() || $(this).closest('.a-hidden').length) return;
						types.push($(this).val());
					});
					if (types.length) rule.type_id = {value: types};
					$('.rule-props .prop-select', form).each(function() {
						if (!$(this).val() || $(this).closest('.a-hidden').length) return;
						var key = $(this).val();
						var cont = $(this).closest('.white-block-row');
						var value = $('.value-input', cont).val();
						var min = $('.min-input', cont).val();
						var max = $('.max-input', cont).val();
						$('.cbx-value', cont).each(function() {
							if ($(this).is(':checked')) {
								if (!value) value = [];
								value.push($(this).val());
							}
						});
						if (value !== '' || min !== '' || max !== '') {
							var prop = {};
							if (value !== '') prop.value = value;
							if (min !== '') prop.min = min;
							if (max !== '') prop.max = max;
							rule[key] = prop;
						}
					});
					var req = {
						type_id: typeId,
						rule: rule
					};
					if ($('.rule-input', form).val()) req.rule_id = $('.rule-input', form).val();
					ui.form.submit(form, {
						url: '/catalog-type/saveDynamicRule/',
						method: 'POST',
						data: req,
						success: function(res) {
							if (res.content) {
								rulesList.html(res.content);
								editContent.close();
								$(window).resize();
								ui.initAll();
								var count = $('#items .items-list .white-body .wblock').length;
								$('.tabs .count-items').text(count);
							}
						},
						errors: function(errors) {
							message.errors({errors: errors});
						},
						servererror: function(err) {
							message.errors({
								text: 'Ошибка сервера: ' + err.status,
								descr: (err.status === 404 || err.status === 200)? '' : err.responseText
							});
						}
					});
					return false;
				});
			};
			
			// задать правило
			$('#rules .actions-panel .action-add').click(function() {
				editContent.open({
					getform: '/catalog-type/dynamicRuleFields/',
					getformtype: 'json',
					getformdata: {
						type_id: typeId
					},
					loadform: function() {
						editRules.call(this);
					},
					customform: true
				});
			});
			
			// редактировать правило
			rulesList.on('click', '.action-edit', function() {
				var id = $(this).closest('.wblock').data('id');
				editContent.open({
					getform: '/catalog-type/dynamicRuleFields/',
					getformtype: 'json',
					getformdata: {
						type_id: typeId,
						rule_id: id
					},
					loadform: function() {
						editRules.call(this);
					},
					customform: true
				});
				return false;
			});
			
			// удалить правило
			rulesList.on('click', '.action-delete', function() {
				var id = $(this).closest('.wblock').data('id');
				message.confirm({
					text: 'Подтвердите удаление правила.',
					type: 'delete',
					ok: function() {
						$.post('/catalog-type/deleteDynamicRule/', {
							type_id: typeId,
							id: [id]
						}, function(res) {
							if (res.content) {
								rulesList.html(res.content);
								$(window).resize();
								ui.initAll();
							}
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
			
			// удалить выбранные правила
			$('#rules .actions-panel .action-delete').click(function() {
				var ids = []; 
				$('.check-item:checked', rulesList).each(function() {
					ids.push($(this).closest('.wblock').data('id'));
				});
				message.confirm({
					text: 'Подтвердите удаление правил.',
					type: 'delete',
					ok: function() {
						$.post('/catalog-type/deleteDynamicRule/', {
							type_id: typeId,
							id: ids
						}, function(res) {
							if (res.content) {
								rulesList.html(res.content);
								$(window).resize();
								ui.initAll();
							}
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
		}());


		/* СВОЙСТВА */
		(function() {
			var propForm = $('#properties .properties-form');
			var propsList = $('.properties-list', propForm);

			// создание свойства
			$('#properties .actions-panel .action-add').click(function() {
				editContent.open({
					getform: '/catalog-type/editProp/',
					getformtype: 'json',
					getformdata: {
						type_id: $('INPUT[name=type_id]', propForm).val()
					},
					loadform: function() {
						editProperty();
						ui.initAll();
					},
					success: function(res) {
						propsList.html(res.content);
						editContent.close();
						ui.initAll();
						$('.tabs .count-properties').text($('.wblock:not(.white-header)', propsList).length);
					},
					errors: function(errors, res) {
						message.errors({
							errors: errors,
							errorsTextHandler: function(err) {
								if (err.key.match(/title\[.*\]/gi) && err.error === 'empty') {
									return 'Название свойства должно быть заполнено.';
								} else if (err.key === 'key' && err.error === 'already_exists') {
									return 'Ключ свойства уже занят.';
								}
							}
						});
					}
				});
				return false;
			});

			// редактирование свойства
			$('#properties').on('click', '.properties-list .prop-title', function() {
				var id = $(this).closest('.wblock').data('prop_id');
				editContent.open({
					getform: '/catalog-type/editProp/',
					getformmethod: 'get',
					getformtype: 'json',
					getformdata: {id: id},
					loadform: function() {
						editProperty();
						ui.initAll();
					},
					success: function(res) {
						propsList.html(res.content);
						editContent.close();
						ui.initAll();
					},
					errors: function(errors, res) {
						message.errors({
							errors: errors,
							errorsTextHandler: function(err) {
								if (err.key.match(/title\[.*\]/gi) && err.error === 'empty') {
									return 'Название свойства должно быть заполнено.';
								} else if (err.key === 'key' && err.error === 'empty') {
									return 'Ключ свойства должнен быть заполнен.';
								} else if (err.key === 'key' && err.error === 'already_exists') {
									return 'Ключ свойства уже занят.';
								}
							}
						});
					}
				});
				return false;
			});

			// удаление выбранных свойств
			$('#properties .actions-panel .action-delete').click(function() {
				if ($(this).hasClass('m-inactive')) return false;
				message.confirm({
					text: 'Подтвердите удаление выбранных свойств.',
					type: 'delete',
					ok: function() {
						propForm.attr('action', '/catalog-type/delProps/').ajaxSubmit({
							dataType: 'json',
							success: function(res) {
								propsList.html(res.content);
								ui.initAll();
								$('.tabs .count-properties').text($('.wblock:not(.white-header)', propsList).length);
							}
						});
					}
				});
				return false;
			});

			// ограничение видимости enum значений родительских свойств			
			propForm.on('click', '.property_available', function() {
				var prop = $(this).closest('.wblock');
				editContent.open({
					getform: '/catalog-type/propertyAvailable/',
					getformdata: {
						type_id: typeId,
						prop_id: $('INPUT[name=prop_id]', prop).val()
					},
					loadform: function() {
						var form = $(this);
						var enumList = $('.prop-avail-cont', form);
						var enumListProps = $('.enum-props', enumList);
						var enumListOrigin = $('.origin', enumListProps);
						var enumListAdd = $('.add-value', enumListProps);
						var sending = false;
						var addToEnum = function() {
							var name = $('INPUT', enumListAdd).val();
							if (!name && sending) return false;
							var origin = enumListOrigin.clone();
							sending = true;
							$.post('/catalog-type/addEnumValueToType/', {
								prop_id: $('.input-prop-id', enumList).val(),
								value: name
							}, function(res) {
								sending = false;
								if (res.error) {
									message.errors(res.error);
								} else {
									var id = res.id;
									$('SPAN', origin).text(name);
									$('INPUT', origin).val(id).attr('name', 'ids[' + id + ']');
									enumListAdd.before(origin.removeClass('origin a-hidden'));
									$('INPUT', enumListAdd).val('').blur();
									$(window).resize();
								}
							}, 'json').error(function(err) {
								sending = false;
								message.serverErrors(err);
							});
							return false;
						};
						$('.action-add', enumListAdd).click(addToEnum);
						$('INPUT', enumListAdd).keydown(function(e) {
							if (e.keyCode === 13) {
								addToEnum();
								return false;
							}		
						});
						$('.unset-all', enumList).click(function() {
							$('.used-prop:not(.origin) INPUT:checkbox', enumListProps).removeAttr('checked');
							return false;
						});
					}, 
					success: function() {
						editContent.close();
					}
				});
				return false;
			});

			// фиксация свойств
			var fixTexts = ['no', 'fix' ,'hide', 'lock'];
			propForm.on('click', '.fix_prop_button', function() {
				var prop = $(this).closest('.wblock');
				var btn = $(this);
				editContent.open({
					getform: '/catalog-superadmin/fixProperty/',
					getformdata: {
						id: $('INPUT[name=prop_id]', prop).val()
					},
					loadform: function() {
						var form = $(this);
						form.on('change', 'INPUT[name=fix]', function() {
							if ($(this).val() !== 3) {
								$('.enum-props-cont INPUT', form).removeAttr('checked');
							}
						});
						form.on('change', '.enum-props-cont INPUT', function() {
							$('INPUT[name=fix][value=3]', form).attr('checked', 'checked');
						});
					},
					datatype: null,
					success: function(res) {
						var form = $(this);
						if (!res) {
							btn.text(fixTexts[$('INPUT[name=fix]:checked', form).val()]);
							editContent.close();
						}
					}
				});
			});

			// сортировка свойств
			var propSortable = function() {
				ui.sortable($('.sortable', propsList), {
					sortableOptions: {
						items: '.wblock:not(.white-header)'
						//containment: 'parent'
					}
				});
			};
			propSortable();
			propsList.on('ui-sortable-sorted', propSortable);
		}());


		/* ГРУППЫ */
		(function() {
			var groupsList = $('#groups .groups-list');

			// добавление
			$('#groups .aside-panel .action-add').click(function() {
				editContent.open({
					getform: '/catalog-type/propGroupFields/',
					getformdata: {
						type_id: typeId
					},
					getformtype: 'json',
					url: '/catalog-type/addPropGroup/',
					success: function(res) {
						groupsList.html(res.content);
						$('.tabs .count-groups').text($('.wblock', groupsList).length);
						editContent.close();
					}
				});
				return false;
			});
		
			// изменение
			groupsList.on('click', '.edit-group', function() {
				var btn = $(this);
				editContent.open({
					getform: '/catalog-type/propGroupFields/',
					getformdata: {
						group_id: btn.data('group-id'),
						type_id: typeId
					},
					getformtype: 'json',
					success: function(res) {
						groupsList.html(res.content);
						editContent.close();
					}
				});
				return false;
			});

			// удаление
			groupsList.on('click', '.action-delete', function() {
				var groupId = $(this).data('group-id');
				message.confirm({
					text: 'Подтвердите удаление группы свойств.',
					target: $(this).closest('.wblock'),
					type: 'delete',
					ok: function() {
						$.post('/catalog-type/delPropGroup/', {
							type_id: typeId,
							group_id: groupId
						}, function(res) {
							if (res.errors) {
								message.errors(res);
								return;
							}
							groupsList.html(res.content);
							$('.tabs .count-groups').text($('.wblock', groupsList).length);
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});
		}());


		/* ОПИСАНИЕ */
		$('.description .actions-panel .action-save').click(function(){
			var postForm = $(this).closest('.description').find('.post-form');
			ui.form.submit(postForm, {
				datatype: 'html',
				success: function() {
					if ($('TEXTAREA[name="text"]', postForm).val()) {
						$('.tabs .count-description').html('<i class="action-icon icon-check"></i>');
					} else {
						$('.tabs .count-description').text('-');
					}
					message.ok('Описание сохранено.');
				},
				errors: function(errors) {
					message.errors({errors: errors});
				},
				serverError: function(err) {
					message.serverErrors(err);
				}
			});
			return false;
		});


		/* ОБЛОЖКА */
		ui.form($('#cover .upload-file-cover'), {
			success: function(res) {
				$('.prop-item .row:not(".add-row")', this).html(res.content);
				if( $('.prop-item .row:not(".add-row") IMG', this).length ){
					$(".add-row I", this).attr({class:"icon-replace"});
					$(".add-row .small-descr", this).text("Заменить изображение");
				} else {
					$(".add-row I", this).attr({class:"icon-add"});
					$(".add-row .small-descr", this).text("Добавить изображение");
				}
				if (!$(this).hasClass('main-cover')) return;
				if ($('.cover-image IMG', this).length) {
					$('#tabs .num.cover').text('+');
				} else {
					$('#tabs .num.cover').text('-');
				}
			},
			errors: function(errors) {
				message.errors({errors: errors});
			},
			serverError: function(err) {
				message.serverErrors(err);
			}
		});

		// релоад обложки
		$('#cover .upload-file-cover').on('change', 'INPUT[type=file]', function() {
			$(this).closest('.upload-file-cover').submit();
		});

		// удаление обложки
		$('#cover .upload-file-cover').on('click', '.delete-cover', function() {
			var form = $(this).closest('.upload-file-cover');
			message.confirm({
				text: 'Подтвердите удаление изображения.',
				type: 'delete',
				ok: function() {
					$('INPUT[type=file]', form).val('');
					form.submit();
					if( $('.prop-item .row:not(".add-row") IMG', form).length ){
						$(".add-row I", form).attr({class:"icon-replace"});
						$(".add-row .small-descr", form).text("Заменить изображение");
					} else {
						$(".add-row I", form).attr({class:"icon-add"});
						$(".add-row .small-descr", form).text("Добавить изображение");
					}
				}
			});
			return false;
		});
		
	});
});