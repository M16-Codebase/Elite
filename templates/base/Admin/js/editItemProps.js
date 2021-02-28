define(['ui', 'editContent', 'editor', 'picUploader', 'message', 'uploadFile', 'imgpreview', 'filter', 'poly'], function(ui, editContent, editor, picUploader, message, uploadFile, imgpreview, filter, Poly) {

	var initProps = function(opt) {
		var forms = $('.edit_properties_form');
		opt = opt || {};

		// контекст
		var changeShownContext = function(el, show, fast) {
			if (show) {
				el.removeClass('m-delete');
				if (fast) el.css({display: 'block'});
				else el.stop().slideDown(function() {
					$(window).resize();
				});
				$(window).resize();
			} else {
				el.addClass('m-delete');
				if (fast) el.css({display: 'none'});
				else el.stop().slideUp(function() {
					$(window).resize();
				});
				$(window).resize();
			}
			if (el.closest('.props-group').length) {
				var group = el.closest('.props-group');
				if ($('.prop-item-cont', group).length === $('.prop-item-cont.m-delete', group).length) {
					group.addClass('m-delete').css({display: 'none'});
				} else {
					group.removeClass('m-delete').css({display: 'block'});
				}
			}
		};

		// видимость варианта
		var classArray = {'0': 'show', '2': 'hide'};
		var dropdown = function() {
			$('.variants-page').each(function() {
				ui.dropdown($('.variant-show-dropdown', this),{
					select: function() {
						var selectItem = $(this);
						var dropTgl = selectItem.closest('.dropdown-menu').prev();
						var type = selectItem.data('type');
						message.confirm({
							text: 'Подтвердите изменение видимости.',
							ok: function() {
								$.post('/catalog-item/changeVisible/', {
									id: selectItem.closest('.one-variant').data('variant-id'),
									value: selectItem.data('type') ? 0 : 1,
									entity: 'variant'
								}, function(res) {
									if (res.errors) {
										message.errors(res);
										return false;
									}
									dropTgl.find('span').text(selectItem.text());
									dropTgl.find('i').attr('class', 'icon-' + classArray[type]);
								}, 'json').error(function(err) {
									message.serverErrors(err);
								});
							}
						});
					}
				});
			});
		}();
		forms.each(function() {
			if ($(this).data('itemProps-init')) return;
			$(this).data('itemProps-init', true);
			var form = $(this);

			// контекст
			var context = {};
			var showContext = {};
			$('.prop-item-cont[data-context]', this).each(function() {
				var cont = $(this);
				var prop = cont.data('prop-key');
				var rules = cont.data('context').split('|');
				if (!$('[name="' + prop + '"]', form).length) return;
				if (!context[prop]) context[prop] = {};
				for (var i in rules) {
					var rule = rules[i].split(':');
					var val = rule[0];
					var keys = rule[1].split(',');
					var action = rule[2];
					if (!val || val === '*') val = 'Выберите...';
					if (action === '1' || action === 'show') action = 1;
					else if (action === '0' || action === 'hide') action = 0;
					else continue;
					if (!context[prop][val]) context[prop][val] = {};
					for (var j in keys) {
						var el = $('.prop-item-cont[data-prop-key="' + keys[j] + '"]', form);
						if (!el.length) continue;
						if (action) {
							if (!showContext[keys[j]]) showContext[keys[j]] = {};
							showContext[keys[j]][prop] = val;
							changeShownContext(el, 0, true);
						}
						context[prop][val][keys[j]] = action;
					}					
				}
			});
			var checkProp = function(name) {
				var el = $(this);
				var valCurrent = '';
				if (el.is('SELECT')) {
					valCurrent = $.trim($('OPTION:selected', el).text());
				} else if (el.is(':checkbox:checked')) {
					valCurrent = $.trim($(this).closest('LABEL').text());
				} else return;
				for (var val in context[name]) {
					for (var key in context[name][val]) {
						var prop = $('.prop-item-cont[data-prop-key="' + key + '"]', form);
						var show = context[name][val][key];
						if (valCurrent === val) {
							if (show) {
								if (!showContext[key] || showContext[key][name] === valCurrent) changeShownContext(prop, 1);
								else continue;
							} else {
								changeShownContext(prop, 0);
							}
						} else {
							if (show) {
								changeShownContext(prop, 0);
							} else {
								if (!showContext[key] || showContext[key][name] === valCurrent) changeShownContext(prop, 1);
								else continue;
							}
						}
					}
				}
			};
			for (var n in context) {
				(function() {
					var name = n;
					$('[name="' + name + '"]', form).each(function() {
						checkProp.call(this, name);
					}).change(function() {
						checkProp.call(this, name);
					});
				})();								
			}


			// замена в инпутах
			$('INPUT, TEXTAREA', form).on('keyup blur', function() {			
				var text = $(this).val();
				var newText = text.replace(/([\s\/])([mм])2/g, '$1$2?').replace(/\s[оo][СC]/g, ' °С');
				if (text !== newText) $(this).val(newText);
			});
			
			
			
			
			
			var req = Create();
			var pstid = $('#postid').prop('value');
			PuskG(pstid);			
			
			

			// Отправка
			form.on('click', '.actions-panel .action-save', function() {
				var comiss = $('#comiss').prop('value');
				if($('#is_arda').prop('checked')) {
				  console.log("Флажок установлен |"+pstid);
				  Pusk('1',pstid,comiss);
				} else {
				  console.log("Флажок не установлен |"+pstid);
				  Pusk('0',pstid,comiss);
				}
				$(this).closest(form).submit();
				return false;
			});
			form.on('keydown', function(e) {
				if ($(e.target).is('INPUT') && e.keyCode === 13) return false;
			}).on('submit', function() {
				if ($(this).hasClass('sending')) return false;
				var form = $(this);
				var req = {properties: {}};
				$('INPUT, SELECT, TEXTAREA', form).each(function() {
					var val = {};
					var el = $(this);
					var valId = el.data('val-id') || 0;
					var segmentId = el.data('segment') || 0;
					var propId = el.attr('name') || el.closest('SELECT').attr('name');
					var multi = el.closest('.multi-item').length;
					if (el.is(':disabled')) return;
					if (el.is('.not-send') || el.closest('.not-send').length) return;
					if (el.is('.a-hidden') || el.closest('.a-hidden').length) return;
					if (el.is(':radio:not(:checked)')) return;
					if (!propId) return;
					if (!req.properties[segmentId]) req.properties[segmentId] = {};
					if (!req.properties[segmentId][propId]) req.properties[segmentId][propId] = multi? {} : [];
					var values = req.properties[segmentId][propId];
					val.val_id = valId;
					if (el.is(':checkbox')) {
						val.value = el.is(':checked')? el.val() : '';
					} else {
						val.value = el.val() || '';
						if ((multi || el.is(':radio')) && val.value === '' && valId) {
							if (!val.options) val.options = {};
							val.options['delete'] = 1;
						}
					}
					if ((el.data('delete') || el.is('.m-delete') || el.closest('.m-delete').length) && valId) {
						if (!val.options) val.options = {};
						val.options['delete'] = 1;
					}
					if (multi) values[el.closest('.multi-item').index() - 1] = val;
					else values.push(val);
					if (el.closest('.field').next('.new-enum-field').length) {
						var newEnum = el.closest('.field').next('.new-enum-field');
						var newVal = {};
						newVal.val_id = valId;
						newVal.value = {};
						$('INPUT', newEnum).each(function() {
							var sId = $(this).data('segment');
							newVal.value[sId] = $(this).val();
						});
						values.push(newVal);
					}
				});
				if ($('.input-item-key', form).length && $('.input-item-key', form).val()) {
					req.key = $('.input-item-key', form).val();
				}
				//console.dir(req); return false;
				req = {q: JSON.stringify(req)};
				$('INPUT[type="hidden"], .special-send', form).each(function() {
					if ($(this).closest('.object-prop').length) return;
					if ($(this).is(':disabled')) return;
					if (!$(this).attr('name')) return;
					req[$(this).attr('name')] = $(this).val();
				});
				if (form.data('id')) {
					req.id = form.data('id');
				}
				if (form.data('variant-id')) {
					req.variant_id = form.data('variant-id');
				}
				if (form.data('type-id')) {
					req.item_type_id = form.data('type-id');
				}
				if (form.data('parent_id')) {
					req.parent_id = form.data('parent_id');
				}
				form.addClass('sending');
				$.post(form.attr('action'), req, function(res) {
					form.removeClass('sending');
					if (!res) {
						message.errors('Ошибка сервера: пустой ответ');
					} else if (res.errors) {
						message.errors({
							errors: res.errors,
							errorsText: {
								'obj:already_changed': 'В данный объект были внесены изменения. Необходимо обновить страницу.'
							},
							errorsTextHandler: function(err) {
								$('[name='+err.key+']', form).addClass('m-error');
								if (err.error === 'necessary') {
									return 'Свойство «'+(err.title || err.key)+'» должно быть заполнено.';
								} else if (err.error === 'unique') {
									return 'Свойство «'+(err.title || err.key)+'» должно быть уникальным.';
								} else if (err.error === 'incorrect') {
									return 'Неверное значение свойства «'+(err.title || err.key)+'».';
								}
							}
						});
					} else if (res.status || res.data.status) {
						if (opt.success) {
							opt.success.call(form, res);
						} else {
							if (res.url) {
								history.replaceState({}, '', res.url);
							}
							if ('variant_id' in req) {
								$('.item-props-edit.m-current #variants').html(res.content);
							} else if ($('.main-tabs').data('catalog') == "config") {
								$('INPUT[name="last_update"]').val(res.data.last_update)
							} else {
								$('.item-props-edit.m-current').html(res.content);
							}
							
							/*/ сабмит фильтра для обновления списка айтемов
							if ($('.edit-content.m-edit-open:not(.m-current) .tab-page.m-current .items-list').length) {
								var page = $('.edit-content.m-edit-open:not(.m-current):last .tab-page.m-current');
								filter.submitHiddenFilter(page);
							} else if ($('.view-content .tab-page.m-current .items-list').length) {
								var page = $('.view-content .tab-page.m-current');
								filter.submitHiddenFilter(page);
							}*/
								
							message.ok('Изменения сохранены.');
							$(window).trigger('initVariants');
							$(window).resize();
							initProps(opt);
							ui.initAll();
						}
					} else form.removeClass('sending');
				}, 'json').error(function(err) {
					form.removeClass('sending');
					message.serverErrors(err);
				});
				return false;
			});


			// объекты
			$('.object-prop-preloader', form).each(function() {
				var prl = $(this);
				$.post('/catalog-item/editObjPropertyPage/', {
					segment_id: prl.data('segment_id') || 0,
					property_id: prl.data('property_id'),
					entity_id: prl.data('entity_id'),
					action: 'view'
				}, function(res) {
					if (res.errors) {
						message.errors(res);
					} else {
						prl.replaceWith(res.content);
						$(window).resize();
						ui.initAll();
					}
				}, 'json').error(function(err) {
					message.serverErrors(err);
				});
			});
			var postTypes = {close: 'show', public: 'show', new: 'draft', hidden: 'hide'};
			var postStatusDropdown = function(){
				ui.dropdown($('.dropdown.post-status'),{
					select: function() {
						var selectItem = $(this);
						var dropTgl = selectItem.closest('.dropdown-menu').prev();
						var type = selectItem.data('type');
						selectItem.closest('.dropdown').find('INPUT[name="status"]').val(type);
						dropTgl.find('span').text(selectItem.text());
						dropTgl.find('i').attr("class", "icon-" + postTypes[type]);
					}
				});
			};
			form.on('click', '.add-object', function() {
				var btn = $(this);
				var row = btn.closest('.add-row');
				var lang = btn.closest('.field').hasClass('segment-object');
				if (lang) {
					editContent.open({
						getform: '/catalog-item/editObjPropertyPage/',
						getformtype: 'json',
						getformdata: {
							segment_id: btn.data('segment-1'),
							property_id: btn.data('property_id'),
							entity_id: btn.data('entity_id')
						},
						loadform: function(res) {
							var form = $(this);
							var req = {
								segment_id: btn.data('segment-2'),
								property_id: btn.data('property_id'),
								entity_id: btn.data('entity_id')
							};
							try {
								req.object_id = res.data.object_ids[btn.data('segment-2')];
							} catch(e) {}
							postStatusDropdown();
							editor($('.redactor-init', form).removeClass('redactor-init').addClass('redactor').blur());
							$.post('/catalog-item/editObjPropertyPage/?second_segment=1', req, function(res) {
								if (res.errors) {
									message.errors(res);
								} else {
									$('.tabs-pages', form).append(res.content);
									editor($('.redactor-init', form).removeClass('redactor-init').addClass('redactor').blur());
									picUploader('.img-uploader-gallery');
									postStatusDropdown();
									$(window).resize();
									ui.initAll();
								}
							}, 'json').error(function(err) {
								message.serverErrors(err);
							});
						},
						url: '/catalog-item/editObjPropertyPage/',
						data: {save: 1},
						beforeSubmit: function() {
							var form = $(this);
							if ($('.file-upload-block', form).length) {
								if (!$('.file-upload-block', form).hasClass('m-uploaded')) {
									uploadFile($('.input-file', form), {
										progressbar: $('.row-progress-bar DIV', form),
										complete: function(data) {
											var name = data.file.name;
											var path = data.path;
											$('.input-filename', form).val(name);
											$('.input-filepath', form).val(path);
											$('.file-upload-block', form).addClass('m-uploaded');
											$('.input-file', form).attr('disabled', true).attr('data-disabled', true);
											form.submit();
											$('.input-file', form).attr('disabled', false).removeAttr('data-disabled');
										},
										error: function(err) {
											message.errors({
												text: 'Ошибка при загрузке файла.',
												descr: err
											});
										}
									});
									return false;
								}
							}
						},
						success: function(res) {
							if (!$('[name=changed]', this).length) {
								$(this).append('<input name="changed" value="1" type="hidden" />');
							}
							$('INPUT[name="last_update"]').val(res.data.last_update);
							if (res.content) {
								var newRow = $(res.content);
								if (!row.hasClass('add-row')) {
									row.replaceWith(newRow);
								} else if (row.closest('.prop-item').hasClass('m-multi')) {
									row.before(newRow);
								} else {
									row.closest('.field').replaceWith(newRow);
								}
								row = newRow;
							}
							message.ok('Изменения сохранены');
							$(window).resize();
							ui.initAll();
						}, 
						errors: function(errors) {
							message.errors({
								errors: errors,
								errorsText: {
									'title:empty': 'Не указан заголовок.',
									'text:empty': 'Не заполнен текст.',
									'text:count_symbols': 'Минимальная длина текста — 10 символов.'
								}
							});
						}
					});
				} else {
					editContent.open({
						getform: '/catalog-item/editObjPropertyPage/',
						getformtype: 'json',
						getformdata: {
							segment_id: btn.data('segment_id') || 0,
							property_id: btn.data('property_id'),
							entity_id: btn.data('entity_id')
						},
						loadform: function() {
							imgpreview();
							editor($('.redactor-init', this).removeClass('redactor-init').addClass('redactor').blur());
							picUploader('.img-uploader-gallery');
							postStatusDropdown();
						},
						url: '/catalog-item/editObjPropertyPage/',
						data: {save: 1},
						beforeSubmit: function() {
							var form = $(this);
							if ($('.file-upload-block', form).length) {
								if (!$('.file-upload-block', form).hasClass('m-uploaded')) {
									uploadFile($('.input-file', form), {
										progressbar: $('.row-progress-bar DIV', form),
										complete: function(data) {
											var name = data.file.name;
											var path = data.path;
											$('.input-filename', form).val(name);
											$('.input-filepath', form).val(path);
											$('.file-upload-block', form).addClass('m-uploaded');
											$('.input-file', form).attr('disabled', true).attr('data-disabled', true);
											form.submit();
											$('.input-file', form).attr('disabled', false).removeAttr('data-disabled');
										},
										error: function(err) {
											message.errors({
												text: 'Ошибка при загрузке файла.',
												descr: err
											});
										}
									});
									return false;
								}
							}
						},
						success: function(res) {
							$('INPUT[name="last_update"]').val(res.data.last_update);
							if (!row.hasClass('add-row')) {
								row.replaceWith(res.content);
							} else if (row.closest('.prop-item').hasClass('m-multi')) {
								row.before(res.content);
							} else {
								row.closest('.field').replaceWith(res.content);
							}
							editContent.close();
							$(window).resize();
							ui.initAll();
						}, 
						errors: function(errors) {
							message.errors({
								errors: errors,
								errorsText: {
									'title:empty': 'Не указан заголовок.',
									'text:empty': 'Не заполнен текст.',
									'text:count_symbols': 'Минимальная длина текста — 10 символов.'
								}
							});
						}
					});
				}
				return false;
			});
			form.on('click', '.edit-object', function() {
				var btn = $(this);
				var row = btn.closest('.row');
				var lang = btn.closest('.field').hasClass('segment-object');
				if (lang) {
					var input1 = $('.input-object:first', row);
					var input2 = $('.input-object:last', row).not(input1);
					editContent.open({
						getform: '/catalog-item/editObjPropertyPage/',
						getformtype: 'json',
						getformdata: {
							segment_id: input1.data('segment'),
							property_id: btn.data('property_id'),
							entity_id: btn.data('entity_id'),
							object_id: input1.val()
						},
						loadform: function() {
							var form = $(this);
							var req = {
								segment_id: input2.length? input2.data('segment') : $('.empty-segment-object', row).data('segment'),
								property_id: btn.data('property_id'),
								entity_id: btn.data('entity_id')
							};
							if (input2.length) {
								req.object_id = input2.val();
							}
							postStatusDropdown();
							editor($('.redactor-init', form).removeClass('redactor-init').addClass('redactor').blur());
							$.post('/catalog-item/editObjPropertyPage/?second_segment=1', req, function(res) {
								if (res.errors) {
									message.errors(res);
								} else {
									$('.tabs-pages', form).append(res.content);
									editor($('.redactor-init', form).removeClass('redactor-init').addClass('redactor').blur());
									picUploader('.img-uploader-gallery');
									postStatusDropdown();
									$(window).resize();
									ui.initAll();
								}
							}, 'json').error(function(err) {
								message.serverErrors(err);
							});
						},
						url: '/catalog-item/editObjPropertyPage/',
						data: {
							changed: 1,
							save: 1
						},
						beforeSubmit: function() {
							var form = $(this);
							if ($('.file-upload-block', form).length && $('.input-file', form)[0].files.length) {
								if (!$('.file-upload-block', form).hasClass('m-uploaded')) {
									uploadFile($('.input-file', form), {
										progressbar: $('.row-progress-bar DIV', form),
										complete: function(data) {
											var name = data.file.name;
											var path = data.path;
											$('.input-filename', form).val(name);
											$('.input-filepath', form).val(path);
											$('.file-upload-block', form).addClass('m-uploaded');
											$('.input-file', form).attr('disabled', true).attr('data-disabled', true);
											form.submit();
											$('.input-file', form).attr('disabled', false).removeAttr('data-disabled');
										},
										error: function(err) {
											message.errors({
												text: 'Ошибка при загрузке файла.',
												descr: err
											});
										}
									});
									return false;
								}
							}
						},
						success: function(res) {
							$('INPUT[name="last_update"]').val(res.data.last_update);
							if (res.content) {
								var newRow = $(res.content);
								if (btn.closest('.field.m-gallery').length) {
									btn.closest('.field.m-gallery').replaceWith(newRow);
								} else {
									row.replaceWith(newRow);
								}
								row = newRow;
							}
							message.ok('Изменения сохранены');
							$(window).resize();
							ui.initAll();
						}, 
						errors: function(errors) {
							message.errors({
								errors: errors,
								errorsText: {
									'title:empty': 'Не указан заголовок.',
									'text:empty': 'Не заполнен текст.',
									'text:count_symbols': 'Минимальная длина текста — 10 символов.'
								}
							});
						}
					});
				} else {
					editContent.open({
						getform: '/catalog-item/editObjPropertyPage/',
						getformtype: 'json',
						getformdata: {
							segment_id: btn.data('segment_id') || 0,
							property_id: btn.data('property_id'),
							entity_id: btn.data('entity_id'),
							object_id: btn.data('object_id')
						},
						loadform: function() {
							imgpreview();
							editor($('.redactor-init', this).removeClass('redactor-init').addClass('redactor').blur());
							picUploader('.img-uploader-gallery');
							postStatusDropdown();
						},
						url: '/catalog-item/editObjPropertyPage/',
						data: {
							changed: 1,
							save: 1
						},
						beforeSubmit: function() {
							var form = $(this);
							if ($('.file-upload-block', form).length && $('.input-file', form)[0].files.length) {
								if (!$('.file-upload-block', form).hasClass('m-uploaded')) {
									uploadFile($('.input-file', form), {
										progressbar: $('.row-progress-bar DIV', form),
										complete: function(data) {
											var name = data.file.name;
											var path = data.path;
											$('.input-filename', form).val(name);
											$('.input-filepath', form).val(path);
											$('.file-upload-block', form).addClass('m-uploaded');
											$('.input-file', form).attr('disabled', true).attr('data-disabled', true);
											form.submit();
											$('.input-file', form).attr('disabled', false).removeAttr('data-disabled');
										},
										error: function(err) {
											message.errors({
												text: 'Ошибка при загрузке файла.',
												descr: err
											});
										}
									});
									return false;
								}
							}
						},
						success: function(res) {
							$('INPUT[name="last_update"]').val(res.data.last_update);
							if (btn.closest('.field.m-gallery').length) {
								btn.closest('.field.m-gallery').replaceWith(res.content);
							} else {
								row.replaceWith(res.content);
							}
							editContent.close();
							$(window).resize();
							ui.initAll();
						},
						errors: function(errors) {
							message.errors({
								errors: errors,
								errorsText: {
									'title:empty': 'Не указан заголовок.',
									'text:empty': 'Не заполнен текст.',
									'text:count_symbols': 'Минимальная длина текста — 10 символов.'
								}
							});
						}
					});
				}
				return false;
			});
			form.on('change', '.add-image INPUT', function() {
				var form = $('<form/>');
				var row = $(this).closest('.row');
				var gallery = $(this).closest('.field').find('.row-gallery');
				row.after(row.clone());
				row.appendTo(form);
				ui.form.submit(form, {
					url: '/images/upload/?simple=1',
					success: function(res) {
						gallery.html(res.content).closest('.row').removeClass('a-hidden');
						$(window).resize();
						ui.initAll();
					},
					afterSubmit: function() {
						form.remove();
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
			
			form.on('click', '.add-item-object', function() {
				var btn = $(this);
				var cont = $(this).closest('.field');
				var origin = $('.origin', cont);
				var newItem = origin.clone().removeClass('a-hidden origin').addClass('object-prop');
				$('SELECT', newItem).addClass('title');
				btn.closest('.add-row').before(newItem);
			});
			
			form.on('click', '.apply-object', function() {
				var btn = $(this);
				var cont = $(this).closest('.prop-item-cont');
				var values = '';
				var items = btn.closest('.field').find('.row').length;
				if (!btn.closest('.row').find('.input-values').val()) return false;
					btn.closest('.field').find('.input-values').each(function() {
						if (values) values += ',';
						values = $(this).val();
					});
					$.post(btn.data('url'), {
						segment_id: btn.data('segment_id') || 0,
						property_id: btn.data('property_id'),
						entity_id: btn.data('entity_id'),
						values: values
					}, function(res) {
						if (res.errors) {
							message.errors(res);
							return false;
						} else if (res.data && res.data.id === null) {
							message.errors({text: 'Объект не найден.'});
							return false;
						}
						var content = $(res.content).hide();
						if (content.hasClass('object-user') && form.find('.order-props').length) {
							var reqType = form.find('.order-props').data('type');
							var userErrors = [];
							$('.object-prop', content).each(function() {
								if ($(this).data('type') !== reqType) {
									userErrors.push('Пользователь ID:' + $(this).data('id') + ' не является ' + (reqType === 'fiz'? 'физическим' : 'юридическим') + ' лицом.');
								} else {
									var userdata = $(this).data('userdata');
									if (!userdata) return;
									try {
										userdata = $.parseJSON('{' + userdata + '}');
										for (var i in userdata) {
											if (userdata[i] !== '') {
												$('[name="'+i+'"]', form).val(userdata[i]);
											}
										}
									} catch(e) {
										return;
									}
								}
							});
							if (userErrors.length) {
								message.errors(userErrors);
								return false;
							}
						}
						btn.closest('.prop-item').find('.input-object.m-delete').each(function() {
							$('.row.m-saved .input-object[value=' + $(this).val() + ']', content).closest('.row').remove();
						});
						$('.row:not(.m-saved) .input-object', content).each(function() {
							btn.closest('.prop-item').find('.input-object.m-delete[value=' + $(this).val() + ']').remove();
						});
						if (!$('.row:not(.a-hidden)', content).length) {
							$('.row.a-hidden:first', content).removeClass('a-hidden');
						}
						var newItems = $('.row', content).length;
						if (newItems < items) {
							return;
						}
						btn.closest('.field').fadeOut(300, function() {
							content.insertAfter(btn.closest('.field')).fadeIn(300, function() {
								$(window).resize();
								ui.initAll();
							});
							$(this).remove();
						});
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				return false;
			});
			form.on('keydown', '.field.m-object .input-values', function(e) {
				if (e.keyCode === 13) {
					$(this).closest('.row').find('.apply-object').click();
					return false;
				}
			});
			form.on('click', '.edit-item-object', function() {
				var row = $(this).closest('.row');
				if (!row.hasClass('add-row')) {
					row.fadeOut(300, function() {
						$(this).addClass('a-hidden');
						row.prev('.row').hide().removeClass('a-hidden').fadeIn(300, function() {
							$(window).resize();
						});
					});
				} else {
					row.prev('.row').hide().removeClass('a-hidden').fadeIn(300, function() {
						$(window).resize();
					});
				}
				return false;
			});
			form.on('click', '.delete-object', function() {
				var row = $(this).closest('.row');
				var input = row.find('.input-object');
				message.confirm({
					text: 'Подтвердите удаление объекта.',
					target: row.closest('.wblock'),
					type: 'delete',
					ok: function() {
						if (input.val()) {
							input.addClass('m-delete').prependTo(input.closest('.prop-item'));
						}
						row.fadeOut(300, function() {
							if (!$(this).siblings('.row:not(.a-hidden)').length) {
								$('.input-values', $(this).prev('.row')).val('');
								$(this).prev('.add-row').fadeIn(300, function() {
									$(this).removeClass('a-hidden');
									$(window).resize();
								});
							}
							$(this).closest('.row-cont').remove();
							$(this).remove();
							$(window).resize();
						});
					}
				});
				return false;
			});

			// меню объектов
			form.on('click', '.delete-all', function() {
				var field = $(this).closest('.field');
				message.confirm({
					text: 'Подтвердите удаление всех объектов.',
					target: field.closest('.wblock'),
					type: 'delete',
					ok: function() {
						if (field.hasClass('m-object')) {
							field.find('.row.object-prop').each(function() {
								var row = $(this);
								setTimeout(function() {
									row.fadeOut(500, function() {
										$('.input-object', row).each(function() {
											var input = $(this);
											if (input.val()) {
												input.addClass('m-delete').prependTo(input.closest('.prop-item'));
											}
										});
										row.closest('.row-cont').remove();
										row.remove();
										$(window).resize();
									});
								}, 1);
							});
						} else {
							field.find('.multi-item').each(function() {
								var el = $(this);
								setTimeout(function() {
									el.fadeOut(function() {
										$('INPUT, TEXTAREA, SELECT', this).attr('data-delete', 1);
									});
								}, 1);
							});
						}
					}
				});
				return false;
			});
			form.on('click', '.sort-alph', function() {
				var field = $(this).closest('.field');
				var arr = field.find('>*');
				if (field.hasClass('object-metro') || field.hasClass('m-list')) {
					tinysort(arr, {selector: '.title>OPTION:checked'});
				} else if (field.hasClass('m-object')) {
					tinysort(arr, '.title');
				} else {
					tinysort(arr, {selector: '.title', useVal: true});
				}
				return false;
			});
			form.on('click', '.sort-asc', function() {
				var arr = $(this).closest('.field').find('>*');
				tinysort(arr, {selector: '.title', useVal: true});
				return false;
			});
			form.on('click', '.sort-desc', function() {
				var arr = $(this).closest('.field').find('>*');
				tinysort(arr, {selector: '.title', useVal: true, order: 'desc'});
				return false;
			});


			// чекбоксы
			$('.prop-item.m-cbx .field').each(function() {
				var cbx = $('INPUT:checkbox, INPUT:radio', this);
				var en = $('.en-var', this);
				var check = function() {
					if (cbx.is(':checked')) {
						en.removeClass('a-hidden');
					} else {
						en.addClass('a-hidden');
					}
					if (cbx.is(':radio')) {
						var item = cbx.closest('.prop-item');
						$('.en-var', item).not(en).addClass('a-hidden');
					}
				};
				cbx.change(check);
				check();
			});

			// селекты
			var selectCheck = function(sel) {
				var field = sel.closest('.field');
				var en = $('.en-var', field);
				en.addClass('a-hidden').filter('.opt-' + sel.val()).removeClass('a-hidden');
			};
			$('.prop-item.m-select').each(function() {
				if ($(this).data('select-init')) return;
				$(this).data('select-init', true);
				$(this).on('change', 'SELECT', function() {
					var field = $(this).closest('.field');
					selectCheck($(this));
					if ($('OPTION:selected', this).data('add')) {
						var newField = $('<div class="field new-enum-field not-send"><div class="row"><div class="lang-col w6"><input type="text" data-segment="1" value=""></div><div class="lang-col en-col w6"><input type="text" data-segment="2" value=""></div></div></div>');
						newField.hide().insertAfter(field).slideDown();
						ui.initAll();
					} else {
						field.next('.new-enum-field').slideUp(function() {
							$(this).remove();
						});
					}
				});
				$('SELECT', this).each(function() {
					selectCheck($(this));
				});
			});

			// сборные значения
			var comboCheck = function(inp) {
				var cont = inp.closest('.combo-cont');
				var en = $('.combo-' + inp.data('key'), cont);
				if (cont.hasClass('m-range')) {
					inp = $('INPUT:first', inp.closest('.lang-col'));
					var inp2 = inp.siblings('INPUT');
					var val = inp.val().replace(',', '.');
					var val2 = inp2.val().replace(',', '.');
					if (parseFloat(val2) < parseFloat(val)) val2 = val;
					$('.range-val', cont).addClass('a-hidden');
					inp.val(val);
					inp2.val(val2);
					if (val === '' && val2 === '') {
					} else if (val !== '' && val2 === '') {
						$('.combo-min', cont).text(val);
						$('.min-val', cont).removeClass('a-hidden');
					} else if (val === '' && val2 !== '') {
						$('.combo-max', cont).text(val2);
						$('.max-val', cont).removeClass('a-hidden');
					} else if (val === val2) {
						$('.combo-same', cont).text(val || val2);
						$('.same-val', cont).removeClass('a-hidden');
					} else {
						$('.cmb1', cont).text(val);
						$('.cmb2', cont).text(val2);
						$('.two-val', cont).removeClass('a-hidden');
					}
				} else {
					if (inp.is(':checkbox')) {
						if (inp.is(':checked')) {
							en.show();
						} else {
							en.hide();
						}
					} else {
						if (inp.val() !== '') {
							$('SPAN', en).text(inp.val());
							en.show();
						} else {
							$('SPAN', en).text('');
							en.hide();
						}
					}
				}
			};
			$('.prop-item.m-combo').each(function() {
				$(this).on('change', 'INPUT', function() {
					comboCheck($(this));
				});
				$('INPUT', this).each(function() {
					comboCheck($(this));
				});
				if ($(this).hasClass('m-range')) {
					var inp = $('INPUT:first', this);
					var inp2 = inp.siblings('INPUT');
					if (inp.val() !== '' && inp2.val() === '') inp2.val(inp.val());
					else if (inp2.val() !== '' && inp.val() === '') inp.val(inp2.val());
				}
			});
			$('.prop-item.m-range .delete-item').each(function() {
				var btn = $(this);
				var field = btn.closest('.field');			
				btn.click(function() {
					$('INPUT', field).val('').change();
				});
			});

			// мульти
			$('.prop-item.m-multi', form).each(function() {
				if ($(this).data('multi-init')) return;
				$(this).data('multi-init', true);
				var cont = $(this);
				var btn = $('.add-button', this);
				var btnAdd = $('.add-button', this).closest('.add-row');
				var origin = $('.multi-item.origin', this);
				var addClass = origin.data('add-class');
				var add = function(fast, val, after) {
					var newItem = origin.clone().removeClass('origin a-hidden').hide();
					$('INPUT:eq(0)', newItem).addClass(addClass);
					$('.multi-chosen', newItem).addClass('chosen');
					if (val) {
						$('.chosen', newItem).val(val);
						selectCheck($('.chosen', newItem));
					}
					if (after) newItem.insertAfter(after).slideDown();
					else newItem.insertBefore(btnAdd).slideDown();
					if (fast) newItem.show();
					else newItem.slideDown();
					ui.initAll();
					$(window).resize();
				};
				btn.click(function() {
					add();
				});
				if (!$('.multi-item:not(.origin)', this).length) {
					add(true);
				}

				cont.on('click', '.delete-item', function() {
					var delBtn = $(this);
					message.confirm({
						text: 'Подтвердите удаление объекта.',
						target: delBtn.closest('.wblock'),
						type: 'delete',
						ok: function() {
							delBtn.closest('.multi-item').slideUp(function() {
								$('INPUT, TEXTAREA, SELECT', this).attr('data-delete', 1);
							});
						}
					});
					return false;
				});
			});
			
			// Диапазон дат
			$('.date-range', form).each(function() {
				if ($(this).data('date-range-init')) return;
				$(this).data('date-range-init', true);
				var cont = $(this);
				var input1 = $('.datepicker', cont).first();
				var input2 = $('.datepicker', cont).last();
				$('.datepicker', cont).on('change', function() {
					var d1 = input1.datepicker('getDate');
					var d2 = input2.datepicker('getDate');
					if (!d1 && !d2) {
						input1.datepicker('option', 'maxDate', null);
						input2.datepicker('option', 'minDate', null);
					} else if (d1 && !d2) {
						input1.datepicker('option', 'maxDate', null);
						input2.datepicker('option', 'minDate', d1);
					} else if (d2 && !d1) {
						input1.datepicker('option', 'maxDate', d2);
						input2.datepicker('option', 'minDate', null);
					} else if (d1 && d2) {
						if (d1 <= d2) {
							input1.datepicker('option', 'maxDate', d2);
							input2.datepicker('option', 'minDate', d1);
						} else {
							input1.datepicker('setDate', d2).datepicker('option', 'maxDate', d1);
							input2.datepicker('setDate', d1).datepicker('option', 'minDate', d2);
						}
					}
				});
			});
			
			// карта
			if (('ymaps' in window) && ymaps.ready) {
				ymaps.ready(function() {
					var popup = $('.set-coords-form');
					var showMap = function(coords, input) {
						var newCoords = '';
						editContent.open({
							form: popup,
							loadform: function() {
								var form = this;
								var map = {};
								var popupMap = $('.map', form);
								var popupInput = $('.coords-input', form);
								popupMap.empty();
								popupInput.val(coords.join(', '));
								map = new ymaps.Map (popupMap[0], {
									behaviors: ['default', 'scrollZoom'],
									center: [coords[0], coords[1]],
									zoom: 15
								});
								var myPlacemark = new ymaps.Placemark([coords[0], coords[1]], {}, {
									draggable: true
								});
								myPlacemark.events.add('drag', function() {
									newCoords = myPlacemark.geometry.getCoordinates();
									newCoords = [newCoords[0].toFixed(6), newCoords[1].toFixed(6)];
									popupInput.val(newCoords.join(', '));
								});
								map.geoObjects.add(myPlacemark);
								map.controls.add('typeSelector').add('smallZoomControl');
								$('.action-save', form).off().on('click', function() {
									if (newCoords) input.val(newCoords);
									$('.action-back', form).click();
									return false;
								});
							}
						});
					};
					var getCoords = function(adr, callback, err) {
						adr = 'Санкт-Петербург ' + adr;
						callback = callback || function() {};
						ymaps.geocode(adr).then(function(res) {
							if (res.geoObjects.get(0)) {
								callback(res.geoObjects.get(0).geometry.getCoordinates(), err);
							} else if (adr !== 'Санкт-Петербург') {
								getCoords(false, callback, true);
							} else {
								callback(false);
							}
						}, function(err) {});
					};
					
					$('.field.m-address', form).each(function() {
						if ($(this).data('map-init')) return;
						$(this).data('map-init', true);
						var addressInput = $('.address-input', this);
						var coordsInput = $('.coords-input', this);
						var marker = $('.set-marker', this);
						addressInput.on('change blur', function() {
							var input = $(this);
							if (coordsInput.val() || !input.val() || input.val().length < 4) return false;
							getCoords(input.val(), function(newCoords, err) {
								if (err) return;
								coordsInput.val(newCoords.join(', '));
							});
						});
						marker.click(function() {
							var coords = coordsInput.val();
							if (!coords) {
								var t = addressInput.val()? addressInput.val() : false;
								getCoords(t, function(newCoords) {
									if (!newCoords) return;
									showMap(newCoords, coordsInput);
								});
							} else {
								if (!coords.match(/^-?\d+?\.?\d*?\s*,?\s*-?\d+?\.?\d*$/gi)) {
									getCoords(false, function(newCoords) {
										showMap(newCoords, coordsInput);
									});
								} else {
									coords = coords.split(',');
									showMap(coords, coordsInput);
								}
							}
							return false;
						});
					});
				});
			} else {
				console.log('No yaMaps');
			}
			
			// полигоны
			$('.field.m-poly', form).each(function() {
				if ($(this).data('poly-init')) return;
				$(this).data('poly-init', true);
				var cont = $(this);
				cont.on('click', '.set-poly', function() {
					var marker = $(this);
					var row = marker.closest('.row');
					var coordsInput = $('.coords-input', row);
					var coords = coordsInput.val();
					var img = marker.data('img');
					img = $('<img />', {src: img}).css({maxWidth: '100%'});
					editContent.open({
						form: '.set-poly-form',
						loadform: function() {
							var form = this;
							var popupInput = $('.coords-input', form);
							$('.img', form).empty().append(img);
							popupInput.val(coords);
							$(window).resize();
							new Poly(img, {}, function() {
								var poly = this;
								poly.add({
									coords: coords,
									animation: 200,
									opacity: 0.3,
									hover: {
										opacity: 0.5
									},
									active: {
										fill: '#f30',
										opacity: 0.5
									},
									mouseover: function() {
										$('.mCustomScrollBox').blur();
									}
								}).edit({}, function(path) {
									popupInput.val(path).blur();
								});
								var activeLength = poly.polys.length;
								$(poly.cont).on('click', function(e) {
									var activeLength = 0;
									if (poly.polys.length) {
										for (var i in poly.polys) {
											if (poly.polys[i].editable) activeLength++;
										}
									}
									if (!activeLength) {
										var size = 20;
										var imgW = img.width();
										var imgH = img.height();
										var x = e.offsetX || e.layerX || (e.clientX - $(e.target).offset().left + window.pageXOffset);
										var y = e.offsetY || e.layerY || (e.clientY - $(e.target).offset().top + window.pageYOffset);
										var coords = 
											((x-size)/imgW*100).toFixed(4) + ',' + ((y-size)/imgH*100).toFixed(4) + ' , ' +
											((x+size)/imgW*100).toFixed(4) + ',' + ((y-size)/imgH*100).toFixed(4) + ' , ' +
											((x+size)/imgW*100).toFixed(4) + ',' + ((y+size)/imgH*100).toFixed(4) + ' , ' +
											((x-size)/imgW*100).toFixed(4) + ',' + ((y+size)/imgH*100).toFixed(4);
										 poly.add({
											coords: coords,
											animation: 200,
											opacity: 0.3,
											hover: {
												opacity: 0.5
											},
											active: {
												fill: '#f30',
												opacity: 0.5
											},
											mouseover: function() {
												$('.mCustomScrollBox').blur();
											}
										}).edit({}, function(path) {
											popupInput.val(path).blur();
										});
										activeLength++;
									}
								});
								$('.delete', form).click(function() {
									if (confirm('Очистить строку?')) {
										popupInput.val('');
										activeLength = 0;
										poly.remove();
									}
									return false;
								});
								if (row.hasClass('multi-item')) {
									row.siblings('.row:visible:not(.origin)').each(function() {
										var sCoords = $('.coords-input', this).val();
										if (!sCoords) return;
										poly.add({
											editable: 0,
											fill: '#000',
											stroke: '#000',
											coords: sCoords,
											cursor: 'default',
											'fill-opacity': 0.3
										});
									});
								}
							});
							$('.action-save', form).off().on('click', function() {
								coordsInput.val(popupInput.val());
								$('.action-back', form).click();
								return false;
							});
							$('.mCustomScrollBox').blur();
						}
					});
					return false;
				});
			});

			// пересчитываем размеры окна при раскрытии групп
			$('.props-group', form).on('ui-slidebox-open ui-slidebox-close', function() {
				$(window).resize();
			});

			// костыль для z-index
			var propGroupsLength = $('.props-group', form).length + 1;
			$('.props-group', form).each(function(i) {
				$(this).css({zIndex: propGroupsLength - i});
			});
			var propItemsLength = $('.prop-item-cont', form).length + 1;
			$('.prop-item-cont', form).each(function(i) {
				$(this).css({zIndex: propItemsLength - i});
			});

		});
	};
	initProps();

function ge(id)
{
    return document.getElementById(id);
}

function Create()
{  
    if(navigator.appName == "Microsoft Internet Explorer")
    {  
        req = new ActiveXObject("Microsoft.XMLHTTP");  
    }
    else
    {  
        req = new XMLHttpRequest();  
    }  
return req;  
}  

function Request(query)
{
    req.open('post', '/templates/base/Admin/components/arda_admin.php' , true );
    req.onreadystatechange = Refresh;
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
    req.send(query);  
}
function RequestG(query)
{
    req.open('post', '/templates/base/Admin/components/arda_get.php' , true );
    req.onreadystatechange = RefreshG;
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
    req.send(query);  
}

function Refresh()
{
    var a = req.readyState;  
  
    if( a == 4 )
    {   
        var b = req.responseText;
        console.log(b);
    }
    else
    {  
        console.log('Отправка.........');
		
    }
}
function RefreshG()
{
    var a = req.readyState;  
  
    if( a == 4 )
    {   
        var b = req.responseText;
        console.log(b);
		var rsp = b.split('|')
		if(rsp[0]==1){
			$('#is_arda').prop('checked',true);
		}
		$('#comiss').prop('value',rsp[1]);	
    }
    else
    {  
        console.log('Отправка.........');
		
    }
}
function Pusk(cbstate,psid,coms)
{  
    var query;
    query = 'state='+cbstate+'&id='+psid+'&comiss='+coms;
	console.log(query);
    Request(query);
}
function PuskG(psid)
{  
	var query;
	query = 'id='+psid;
	console.log(query);
	RequestG(query);
}

	return initProps;
});