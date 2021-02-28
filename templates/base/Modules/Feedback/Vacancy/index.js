$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		var list = $('.handling-base');
		var filter = $('.aside-filter FORM');
		
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
		
		list.on('click', '.open-form', function() {
			var id = $(this).data('id');
			editContent.open({
				getform: '/hr-feedback/viewRequest/',
				getformdata: {id: id}
			});
			return false;
		});
		
		list.on('click', '.sort-link', function() {
			var sort = $(this).data('sort');
			var val = $(this).data('val');
			$('.input-sort', filter).attr('name', sort).val(val);
			filter.submit();
			return false;
		});
		
	});
});