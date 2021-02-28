$(function(){
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-url-form',
				clearform: true,
				success: function(res) {
					if (res.errors === null){
						$('.white-body').html(res.content);
						$(window).resize();
						ui.initAll();
						editContent.close();
					} else {
						message.errors(res);
					}
				}
			});
		});
		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var delName = $(this).data('delname');
			message.confirm({
				text: 'Подтвердите удаление ' + delName + '.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/segment-text/deletePageUrl/',
						type: 'post',
						data: {id: id},
						dataType: 'json',
						success: function(res){
							if (res.errors === null){
								$('.white-body').html(res.content);
								$(window).resize();
								ui.initAll();
							} else {
								message.errors(res);
							}
						}
					});
				}
			});
			return false;
		});
		$('.white-body').on('click', '.action-edit', function(){
			var id = $(this).closest('.white-block-row').data('id');
			editContent.open({
				getform: '/segment-text/editUrlFields/',
				getformdata: {
					id: id
				},
				getformtype: 'json',
				success: function(res) {
					if (res.errors === null){
						$('.white-body').html(res.content);
						$(window).resize();
						editContent.close();
					} else {
						message.errors(res);
					}
				}
			});
			return false;
		});
		
	});
});