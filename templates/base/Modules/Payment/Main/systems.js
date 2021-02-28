$(function() {
	require(['ui'], function(ui) {
		
		$('.systems-list').on('change', '.change-used', function() {
			var key = $(this).closest('TR').data('key');
			$.post('/payConfirm/setSystemUsed/', {
				used: $(this).is(':checked')? 1 : 0,
				key: key
			}, function(res) {
			});
		}).on('change', '.change-group', function() {
			var key = $(this).closest('TR').data('key');
			$.post('/payConfirm/setSystemGroup/', {
				group_id: $(this).val(),
				key: key
			}, function(res) {
			});
		});
		
	});
});