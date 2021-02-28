define(['ui', 'message'], function(ui, message) {

	var initEditProp = function() {
		var form = $('.property-form');
		var imageForm = $('.upload-prop-cover');
		if (form.data('edit-inited')) return false;
		form.data('edit-inited', true);

		// сохраняем
		$('.actions-panel .action-save', form).on('click', function() {
			if (parseInt($('INPUT[name="default_mult_change"]', form).val()) === 0){
				message.confirm({
					text: 'При данном изменении свойства его значения будут удалены.',
					ok: function() {
						form.submit();
					}
				});
			} else {
				form.submit();
			}
			return false;
		});

		// прячем ненужные строки
		var dataType = $('SELECT[name=data_type]', form).val();
		var checkFields = function() {
			dataType = $('SELECT[name=data_type]', form).val();

			if ($('INPUT[name=multiple]', form).is(':checked')){
				$('.tab-' + dataType + ' .multiple', form).removeClass('a-hidden');
				$('.tab-' + dataType + ' .not-multiple', form).addClass('a-hidden');
				$('INPUT, TEXTAREA', $('.tab-' + dataType + ' .not_multiple', form)).val('');
			} else {
				$('.tab-' + dataType + ' .multiple', form).addClass('a-hidden');
				$('.tab-' + dataType + ' .not-multiple', form).removeClass('a-hidden');
				$('INPUT, TEXTAREA', $('.tab-' + dataType + ' .multiple', form)).val('');
			}

			if ($('INPUT[name=multiple]', form).is(':checked') && !$('INPUT[name=major]', form).is(':checked')) {
				$('.major-count', form).addClass('a-hidden').find('INPUT').removeAttr('checked');
			} else if (dataType !== 'enum') {
				$('.major-count', form).removeClass('a-hidden');
			}

			if ($('SELECT[name=search_type]', form) === 'none') {
				$('.filter-name', form).addClass('a-hidden');
			} else {
				$('.filter-name', form).removeClass('a-hidden');
			}

			if (dataType === 'flag') {
				$('.search-type', form).addClass('a-hidden');
				$('.sort-type', form).addClass('a-hidden');
				imageForm.removeClass('a-hidden');
			} else {
				$('.search-type', form).removeClass('a-hidden');
				$('.sort-type', form).removeClass('a-hidden');
				imageForm.addClass('a-hidden');
			}

			if (dataType === 'int' || dataType === 'float' || dataType === 'diapasonInt' || dataType === 'diapasonFloat') {
				$('.search-type OPTION.between', form).removeAttr('disabled');
			} else {
				var betweenOpt = $('.search-type OPTION[value=between]', form);
				if (betweenOpt.is(':selected')) {
					betweenOpt.removeAttr('selected');
					$('.search-type OPTION[value=none]', form).attr('selected', 'selected');
				}
				betweenOpt.attr('disabled', 'disabled');
			}

			if (dataType === 'string' || dataType === 'address') {
				$('.search-type OPTION[value=autocomplete]', form).removeAttr('disabled');
			} else {
				var autocompOpt = $('.search-type OPTION[value=autocomplete]', form);
				if (autocompOpt.is(':selected')) {
					autocompOpt.removeAttr('selected');
					$('.search-type OPTION[value=none]', form).attr('selected', 'selected');
				}
				autocompOpt.attr('disabled', 'disabled');
			}

			if (dataType === 'view') {
				$('.prop-necessary', form).addClass('a-hidden');
				$('.prop-necessary INPUT:checkbox', form).removeAttr('checked');
			} else {
				$('.prop-necessary', form).removeClass('a-hidden');
			}

			$('.type-tabs .tab-page:not(.tab-' + dataType + ')', form).find('INPUT:text, TEXTAREA').each(function() {
				var defVal = $(this).data('default') || '';
				$(this).val(defVal);
			});
			$(window).resize();
		};
		checkFields(dataType);


		// изменение типа данных
		ui.tabs($('.type-tabs', form), {
			beforeChange: function(page) {
				$('INPUT[name=set]', form).prop('checked', false);
				dataType = page.replace('.tab-', '');
				checkFields();
				setTimeout(function() {
					$(window).resize();
				}, 50);
			}
		});
		$('INPUT[name=set]', form).change(function() {
			var input = $(this);
			if (input.is(':checked')) {
				checkFields();
			} else {
				message.confirm({
					text: 'При данном изменении свойства его значения будут удалены.',
					target: input.closest('.wblock'),
					ok: function() {
						checkFields();
					},
					cancel: function() {
						input.prop('checked', true);
					}
				});
			}
		});

		// изменение мультипликативности, возможности фильтрации, подбора похожих
		$('INPUT[name=multiple], SELECT[name=search_type], INPUT[name=major]', form).change(function() {
			checkFields();
		});

		// изменение уникальности
		$('INPUT[name=unique]', form).click(function(){
			var checker = $(this);
			if (!checker.prop('checked')) return true;
			$.getJSON('/catalog-type/checkItemValuesOnUnique/', {
				property_id: form.data('property')
			}, function(result){
				if (result.error){
					message.errors(result);
				} else if (result.status){
					checker.prop('checked', 'checked');
				}
			}).error(function(err) {
				message.serverErrors(err);
			});
			return false;
		});

		// вставляем знак {!} в шаблон
		$('.mask-val', form).mousedown(function(){
			var input = $(this).closest('.wblock').find('INPUT:focus');
			if (!input.length) input = $(this).closest('.wblock').find('INPUT:first');
			insertAtCaret(input, $(this).text());
			return false;
		}).click(function() {return false;});

		// вставляем свойство в значение конструктора
		$('SELECT[name=temp_props], SELECT[name=temp_props_variant]', form).change(function(){
			if ($(this).val() && $(this).val() !== '0') {
				insertAtCaret($(this).siblings('TEXTAREA'), '{' + $(this).val() + '}');
			}
		});
        //изменение сегментности
        $('INPUT[name="segment"]', form).change(function(){
            if (!confirm('Все значения данного свойства будут удалены!')){
                if (this.checked){
                    $(this).removeProp('checked');
                }else{
                    $(this).prop('checked', 'checked');
                }
                return false;
            }
        });
		// редактирование enum
		(function() {			
			// добавляет значение для enum
			var addEnum = function() {
				var row = $(this).closest('.white-block-row');
				var cont = $(this).closest('.add-form');
				var value = [];
				$('.input-val', cont).each(function(){
					value.push($(this).val());
				});
				var inputKey = $('.input-key', cont);
				var key = inputKey.val();
				var btn = $('.add', cont);
				if ($('.input-val:eq(0)', cont).val() !== '') {
					var addBlock = $('.origin', row).clone(true).removeClass('origin a-hidden').addClass('one-value');
					cont.before(addBlock);
					addBlock.find('.input-val').each(function(index){
						$(this).val(value[index]);
					});
					addBlock.find('.input-key').val(key);
					$(window).resize();
					$('.input-val', cont).each(function(){
						$(this).val('');
					});
					inputKey.val('');
				}
				return false;
			};
			$('.add-form .input-val:eq(0)', form).on('focus', function() {
				$(this).closest(".add-form").find('.icon-add').css({opacity:1});
			}).on('blur change', function() {
				if (!$(this).val() && !$(this).siblings('INPUT').val()) {
					$(this).closest(".add-form").find('.icon-add').css({opacity:.3});
				}
			}).on('keyup', function(e) {
				if (e.keyCode === 13) {
					if (!$('.add-form .input-val:eq(0)', form).val()) {
						$('.new-enum-value.input-val', form).focus();
					} else {
						$(this).closest(".add-form").find('.add').click();
					}
					return false;
				}
			});
			$('.add-form .add', form).click(addEnum);

			// сортировка enum по алфавиту
			$('.enum-alph-sort', form).on('click', function() {
				var cont = $('.being-values', form);
				var list = [];
				$('.one-value', cont).each(function() {
					list.push({
						val: $('INPUT.input-val:eq(0)', this).val(),
						row: $(this)
					});
				});
				list = _.sortBy(list, function(row) {return row.val;});
				_.each(list, function(item, i) {
					item.row.insertBefore($('.add-form'), form);
				});
			});

			// удаление значения enum
			$('.remove-enum-value, .new-remove-enum-value', form).click(function(){
				var btn = $(this);
				message.confirm({
					text: 'Подтвердите удаление значения.',
					target: btn.closest('.wblock'),
					type: 'delete',
					ok: function() {
						btn.closest('.row').remove();
						$(window).resize();
					}
				});
				return false;
			});
		})();

		// загрузка обложки
		// TODO
		imageForm.on('change', 'INPUT[name=image]', function() {
			ui.form.submit(imageForm, {
				success: function(result) {
					if (result.image_url) {
						var newImage = result.image_url + '?time=' + Date.now();
						$('.prop-cover IMG', imageForm).attr('src', newImage);
						$('.prop-cover', imageForm).removeClass('a-hidden');
					}
				},
				errors: function(errors) {
					message.errors({errors: errors});
				}
			});
		});

		// удаление обложки
		imageForm.on('click', '.delete-cover', function() {
			message.confirm({
				text: 'Подтвердите удаление изображения.',
				type: 'delete',
				ok: function() {
					$.post('/catalog-type/loadPropertyImage/', {
						id: $('INPUT[name=id]', imageForm).val()
					}, function(result) {
						if (!result) {
							$('.prop-cover', imageForm).addClass('a-hidden');
							$('.prop-cover IMG', imageForm).attr('src', '#');
						} else {
							message.errors({
								text: 'Ошибка при удалении обложки',
								descr: result
							});
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				}
			});
		});

		//вставляет текст на место каретки в textarea
		function insertAtCaret(txt,text) {
			if (!txt.length) return;
			var txtarea =txt[0];
			var scrollPos = txtarea.scrollTop;
			var strPos = 0;
			var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
				'ff' : (document.selection ? 'ie' : false ) );
			if (br === 'ie') {
				txtarea.focus();
				var range = document.selection.createRange();
				range.moveStart ('character', -txtarea.value.length);
				strPos = range.text.length;
			}
			else if (br === 'ff') strPos = txtarea.selectionStart;

			var front = (txtarea.value).substring(0,strPos);
			var back = (txtarea.value).substring(strPos,txtarea.value.length);
			txtarea.value=front+text+back;
			strPos = strPos + text.length;
			if (br === 'ie') {
				txtarea.focus();
				var range = document.selection.createRange();
				range.moveStart ('character', -txtarea.value.length);
				range.moveStart ('character', strPos);
				range.moveEnd ('character', 0);
				range.select();
			}
			else if (br === 'ff') {
				txtarea.selectionStart = strPos;
				txtarea.selectionEnd = strPos;
				txtarea.focus();
			}
			txtarea.scrollTop = scrollPos;
		}
	};
	initEditProp();
	
	return initEditProp;

});