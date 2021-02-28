$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		var list = $('.handling-base');
		var filter = $('.aside-filter FORM');
		
		// фильтр
		ui.form(filter, {
			method: 'get',
			ignoreempty: true,
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
		list.on('click', '.sort-link', function() {
			var sort = $(this).data('sort');
			var val = $(this).data('val');
			$('.input-sort', filter).attr('name', sort).val(val);
			filter.submit();
			return false;
		});
		
		// открыть
		list.on('click', '.open-form', function() {
			var id = $(this).closest('.wblock').data('id');
			editContent.open({
				getform: '/feedback/viewRequest/',
				getformdata: {id: id},
				loadform: function() {
					$('.action-delete', this).on('click', function() {
						message.confirm({
							text: 'Подтвердите удаление обращения',
							type: 'delete',
							ok: function() {
								ui.form.submit(filter, {
									url: '/feedback/deleteRequest/',
									data: {request_id: id},
									success: function(res) {
										if (res.content) {
											list.html(res.content);
											ui.initAll();
										}
										editContent.close();
										$(window).resize();
									}
								});
							}
						});
						return false;
					});
				}
			});
			return false;
		});
		
		// удалить
		list.on('click', '.delete-feedback', function() {
			var id = $(this).closest('.wblock').data('id');
			message.confirm({
				text: 'Подтвердите удаление обращения',
				type: 'delete',
				ok: function() {
					ui.form.submit(filter, {
						url: '/feedback/deleteRequest/',
						data: {request_id: id},
						success: function(res) {
							if (res.content) {
								list.html(res.content);
								$(window).resize();
								ui.initAll();
							}
						}
					});
				}
			});
			return false;
		});
		
		// подтвердить, отклонить
		list.on('click', '.action-status', function() {
			var id = $(this).closest('.wblock').data('id');
			var status = $(this).data('status') || 0;			
			$.post('/feedback/setStatus/', {
				status: status,
				id: id
			}, function(res) {
				if (res.errors) {
					message.errors(res);
					return;
				}
				// TODO: проверить, возвращается ли контент
				if (res.content) {
					list.html(res.content);
					$(window).resize();
					ui.initAll();
				}
			}, 'json').error(function(err) {
				message.serverErrors(err);
			});
			return false;
		});
		
	});
});