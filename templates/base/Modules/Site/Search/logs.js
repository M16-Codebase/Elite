$(function() {
	require(['ui', 'editContent'], function(ui, editContent) {
		
		var filter = $('.order-logs');
		var list = $('.list');
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
	});
});