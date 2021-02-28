$(function() {
	require(['editContent', 'message'], function(editContent, message) {
		
		$('.apply-migrations').on('click', function() {
			$.get($(this).attr('href'), function(res) {
				if (res.errors) {
					message.errors(res);
				} else {
					message.ok('Миграции применены (' + res.data.apply_count + ').');
				}
			}, 'json');
			return false;
		});
		
		$('.action-add').on('click', function() {
			editContent.open({
				form: '.add-migration-form',
				clearform: true,
				success: function() {
					window.location.reload();
				}
			});
			return false;
		});
		
	});
});