$(function() {
	require(['ui', 'message'], function(ui, message) {
		
		var list = $('.logs-list');
		var filter = $('.aside-filter .items-filter');
		
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
		
	});
});