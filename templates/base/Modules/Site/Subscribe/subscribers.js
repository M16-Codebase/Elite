$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		var list = $('.list');
		var group_id = $('.viewport').data("group_id");
		var filter = $('.aside-filter FORM');
		
		//check all
		list.on('click', '.check-all', function(){
			var checked = $(this).prop('checked');
			$.each($('.selected-subscriber', list), function(){
				$(this).prop('checked', checked);
			});
		});
	
		// фильтр
		ui.form(filter, {
			method: 'get',
			success: function(res) {
				history.replaceState({}, '', '?' + filter.formSerialize());
				if (res.content) {
					list.html(res.content);
					$(window).resize();
					ui.initAll();
				}
			},
			errors: function(errors) {
				message.errors({errors: errors});
			},
			serverError: function(err) {
				message.serverErrors(err);
			}
		});
		
		// сортировка
		list.on('click', '.white-header .sort-link', function() {
			var sort = $(this).data('sort');
			var val = $(this).data('val');
			$('.input-sort', filter).attr('name', sort).val(val);
			filter.submit();
			return false;
		});
		
		//добавить подписчика
		$('.actions-panel .action-button.action-add').click(function() {
			editContent.open({
				form: '.subscribers-form',
				clearform: true,
				loadform: function(){
//					$('input[name="name"]', this).prop('disabled', true);
//					$('input[name="surname"]', this).prop('disabled', true);
//					$('input[name="company_name"]', this).prop('disabled', true);
//					$('select[name="scope"]', this).prop('disabled', true);
				},
				beforeSubmit: function(){
					if ($(this).hasClass('edit-mode')) {
						ui.form.submit($(this),{
							url: $(this).attr('action'),
							beforeSubmit: function(){},
							success: function(res) {
								list.html(res.content);
								editContent.close();
								$(window).resize();
								ui.initAll();
							}
						});
						return false;
					}
				},
				success: function(res) {
					if (!this.hasClass('edit-mode')) {
						if (res.errors === null){
							var member = res.data.member;
							if (member.name == '' && member.surname == '' && member.company_name == ''){
								$(this).addClass('edit-mode').attr({"action":"/subscribe/editSubscriber/"});
								$('input[name="name"]', this).prop('disabled', false);
								$('input[name="surname"]', this).prop('disabled', false);
								$('input[name="company_name"]', this).prop('disabled', false);
								message.ok('Был создан новый подписчик, заполните необходимые поля')
							} else {
								list.html(res.content);
								editContent.close();
								$(window).resize();
								ui.initAll();
							}
						}
					} else {
						list.html(res.content);
						editContent.close();
						$(window).resize();
						ui.initAll();
					}
				}
			});
			return false;
		});
		//	$('.popup-add-subscriber form').submit(function(evt){
//		evt.preventDefault();
//		var popup = $('.popup-add-subscriber');
//		var add_form = $(this);
//		if (!popup.hasClass('edit-mode')){
//			add_form.ajaxSubmit({
//				url: '/subscribe/addSubscriber/',
//				type: 'post',
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//						var member = res.data.member;
//						if (member.name == '' && member.surname == '' && member.company_name == ''){
//							popup.addClass('edit-mode');
//							$('input[name="name"]', popup).prop('disabled', false);
//							$('input[name="surname"]', popup).prop('disabled', false);
//							$('input[name="company_name"]', popup).prop('disabled', false);
//							$('select[name="scope"]', popup).prop('disabled', false);
//							alert('Был создан новый подписчик, заполните необходимые поля');
//						} else {
//							popup.dialog('close');
//						}
//					} else {
//						if (typeof res.errors.error != 'undefined') {
//							alert('Неизвестная ошибка');
//						}
//					}
//				}
//			});
//		} else {
//			add_form.ajaxSubmit({
//				url: '/subscribe/editSubscriber/',
//				type: 'post',
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//						popup.removeClass('edit-mode');
//						popup.dialog('close');
//					} else {
//
//					}
//				}
//			});
//		}
//	});
		//редактировать подписчика
		list.on('click', '.white-block-row .action-edit.m-active', function() {
            var email = $(this).closest('.white-block-row').data('email');
			editContent.open({
				getform: '/subscribe/subscriberFields/',
				getformdata: {
					email: email, 
				},
				getformtype: 'json',
				success: function(res) {
					list.html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		// удалить список рассылки
		list.on('click', '.white-block-row .action-delete', function() {
			var email = $(this).closest('.white-block-row').data('email');
			var btn = $(this);
			message.confirm({
				text: 'Подтвердите удаление подписчика.',
				type: 'delete',
				ok: function() {
					$.post(btn.attr('href'), {group_id: group_id}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							list.html(res.content);
							$(window).resize();
							ui.initAll();
							customSlidebox();
						}
					}, 'json');
				}
			});
			return false;
		});
		
		//перенести список подписчиков
		$('.actions-panel .action-button.action-another').click(function() {
			var filterParams = $('.white-header').data('filter-params');
			editContent.open({
				getform: '/subscribe/importListPopup/',
				loadform: function(){
					var invInput = $('INPUT[name="target_group_name"]').closest('.white-block-row');
					var form = $(this)
					$(this).on('change', 'SELECT[name="target_group"]', function(){
						if ($(this).val() == 'add-new-list') {
							invInput.removeClass('a-hidden');
						} else {
							invInput.addClass('a-hidden');
						}
					});
					var cont = $('.white-body');
					var selected = $('.selected-subscriber:checked', cont);
					// кол-во выбранных айтемов
					var count = selected.length;
					// очищаем попап от выбранных айтемов
					$('.selected-items', this).empty();
					// добавляем чекбоксы выбранных элементов в .selected-items
					selected.each(function() {
						$('.selected-items', form).append($(this).clone());
					});	
				},
				getformdata:{id: $(this).data('id')},
				data: {id: $(this).data('id'), filter_params:filterParams },
				getformtype: 'json',
				success: function() {
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		list.on('click', '.user-status', function(evt){
			evt.preventDefault();
			var btn = $(this);
			var row = btn.parents('.wblock');
			var status = btn.hasClass('m-active') ? 0 : 1
			$.ajax({
				url: '/subscribe/toggleLockStatus/',
				type: 'post',
				data: {
					email: row.data('email'),
					status: status
				},
				dataType: 'json',
				success: function(res){
					if (res.errors === null){
						if (status == 1) {
							btn.removeClass('m-banned').addClass('m-active').attr('title', 'Разблокировать');
						} else {
							btn.removeClass('m-active').addClass('m-banned').attr('title', 'Заблокировать');
						}
					}
				}
			});
		});
		
		//импорт csv
		$('.actions-panel .action-button.action-import').click(function() {
			editContent.open({
				form: '.subscribers-import-form',
				loadform: function() {
				},
				success: function(res) {
					list.html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		//экспорт csv
		$('.actions-panel .action-button.action-export').click(function() {
			$(this).attr({href:window.location.href + '&export'});
		});
		
		//очистить список
		$('.actions-panel .action-button.action-delete').click(function() {
			message.confirm({
				text: 'Подтвердите удаление всех подписчиков в списке.',
				type: 'delete',
				ok: function() {
					$.post('/subscribe/clearSubscribersList/?id='+group_id, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							list.html(res.content);
							$(window).resize();
							ui.initAll();
						}
					}, 'json');
				}
			});
			return false;
			
		});
			
//	$('.actions-panel .action-add').click(function(){
//		var popup = $('.popup-add-subscriber');
//		$('input[type="text"]', popup).val('');
//		$('input[name="name"]', popup).prop('disabled', true);
//		$('input[name="surname"]', popup).prop('disabled', true);
//		$('input[name="company_name"]', popup).prop('disabled', true);
//		$('select[name="scope"]', popup).prop('disabled', true);
//		popup.removeClass('edit-mode').dialog({
//			title: 'Добавление подписчика'
//		}).dialog('open');
//	});
//	
//	$('.actions-panel .action-clear A').click(function(evt){
//		evt.preventDefault();
//		if (confirm('Вы действительно хотите очистить список?')){
//			$.ajax({
//				url: $(this).attr('href'),
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//					}
//				}
//			});
//		}
//	});
//	$('.actions-panel .action-import A').click(function(evt){
//		evt.preventDefault();
//		$('.popup-window-import-subscribers').dialog({
//			title: 'Импорт подписчиков из CSV-файла'
//		}).dialog('open');
//	});
//	
//	$('.popup-window-import-subscribers FORM').submit(function(evt){
//		evt.preventDefault();
//		$(this).ajaxSubmit({
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					$('#subscribers-container').html(res.content);
//					$('.popup-window-import-subscribers').dialog('close');
//				} else {
//					
//				}
//			}
//		});
//	});
//	
//	$('.popup-add-subscriber form').submit(function(evt){
//		evt.preventDefault();
//		var popup = $('.popup-add-subscriber');
//		var add_form = $(this);
//		if (!popup.hasClass('edit-mode')){
//			add_form.ajaxSubmit({
//				url: '/subscribe/addSubscriber/',
//				type: 'post',
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//						var member = res.data.member;
//						if (member.name == '' && member.surname == '' && member.company_name == ''){
//							popup.addClass('edit-mode');
//							$('input[name="name"]', popup).prop('disabled', false);
//							$('input[name="surname"]', popup).prop('disabled', false);
//							$('input[name="company_name"]', popup).prop('disabled', false);
//							$('select[name="scope"]', popup).prop('disabled', false);
//							alert('Был создан новый подписчик, заполните необходимые поля');
//						} else {
//							popup.dialog('close');
//						}
//					} else {
//						if (typeof res.errors.error != 'undefined') {
//							alert('Неизвестная ошибка');
//						}
//					}
//				}
//			});
//		} else {
//			add_form.ajaxSubmit({
//				url: '/subscribe/editSubscriber/',
//				type: 'post',
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//						popup.removeClass('edit-mode');
//						popup.dialog('close');
//					} else {
//
//					}
//				}
//			});
//		}
//	});
//	
//	$('#subscribers-container').on('click', '.i-delete', function(evt){
//		evt.preventDefault();
//		var btn = $(this);
//		var row = btn.parents('tr');
//		if (confirm('Вы уверены, что хотите удалить подписчика "' + row.data('name') +'"?')){
//			$.ajax({
//				url: btn.attr('href'),
//				type: 'post',
//				data: {group_id: $('#subscribers-container').data('group_id')},
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('#subscribers-container').html(res.content);
//					}
//				}
//			});
//		}
//	});
//	
//	$('#subscribers-container').on('click', '.i-lock', function(evt){
//		evt.preventDefault();
//		var btn = $(this);
//		var row = btn.parents('tr');
//		var status = btn.hasClass('unlock') ? 1 : 0
//		$.ajax({
//			url: '/subscribe/toggleLockStatus/',
//			type: 'post',
//			data: {
//				email: row.data('email'),
//				status: status
//			},
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					btn.toggleClass('unlock').attr('title', status == 1 ? 'Заблокировать' : 'Разблокировать');
//					row.find('.i-user-status').toggleClass('lock').attr('title', status == 1 ? 'Активен' : 'Заблокирован');
//				}
//			}
//		});
//	});
//	
//	$('#subscribers-container').on('click', '.edit-subscriber-btn', function(evt){
//		evt.preventDefault();
//		var btn = $(this);
//		$.ajax({
//			url: '/subscribe/subscriberFields/',
//			type: 'post',
//			data: {email: btn.data('email')},
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null) {
//					var popup = $('.popup-edit-subscriber')
//					popup.html(res.content).dialog('open');
//					$('.group_id', popup).val($(popup).data('group_id'));
//				} else {
//					alert('ПОТРАЧЕНО');
//				}
//			}
//		});
//	});
//	
//	$('.popup-edit-subscriber').on('submit', 'form', function(evt){
//		evt.preventDefault();
//		$(this).ajaxSubmit({
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					$('#subscribers-container').html(res.content);
//					$('.popup-edit-subscriber').dialog('close');
//				} else {
//					
//				}
//			}
//		});
//	});
//	
//	$('.actions-panel .action-add2 A').click(function(evt){
//		evt.preventDefault();
//		$.ajax({
//			url: '/subscribe/importListPopup/',
//			type: 'post',
//			data: {id: $(this).data('id')},
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					$('.popup-import-group').html(res.content).dialog({
//						title: 'Экспорт подписчиков в другой список'
//					}).dialog('open');
//				}
//			}
//		});
//		
//	});
//	
//	$('.popup-import-group').on('submit', 'form', function(evt){
//		evt.preventDefault();
//		var export_form = $('#form-export-subscribers');
//		var subscr_table = $('.subscribers-table');
//		$('input[name="target_group"]', export_form).val($('select[name="target_group"]', $(this)).val());
//		$('input[name="target_group_name"]', export_form).val($('input[name="target_group_name"]', $(this)).val());
//		$('input[name="filter_params"]', export_form).val(subscr_table.data('filter'));
//		$('input[name="sort_params"]', export_form).val(subscr_table.data('sort'));
//		export_form.ajaxSubmit({
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					$('#subscribers-container').html(res.content);
//					$('.popup-import-group').dialog('close');
//				} else {
//					if (typeof res.errors.target_group != 'undefined'){
//						alert('Список не выбран');
//					}else if (typeof res.errors.subscribers != 'undefined'){
//						alert('Подписчики не выбраны');
//					}
//				}
//			}
//		});
//	}).on('change', 'select[name="target_group"]', function(){
//		var select = $(this);
//		var group_name_line = $('#new_group_name_row');
//		$('.default-value', select).prop('disabled', true);
//		if (select.val() == ''){
//			group_name_line.show();
//		} else {
//			group_name_line.hide();
//		}
//	});
//        
//	$('#subscribers-container').on('click', '#select-all', function(){
//		var checked = $(this).attr('checked') == 'checked' ? true : false;
//		$.each($('.selected-subscriber', $('#subscribers-container')), function(){
//			$(this).attr('checked', checked);
//		});
//	});
//	
//	$('.actions-panel .action-sync A').click(function(evt){
//		evt.preventDefault();
//		$.ajax({
//			url: '/subscribe/synchronize/',
//			success: function(){
//				alert('Синхронизация завершена');
//			}
//		});
//	});
	});
});