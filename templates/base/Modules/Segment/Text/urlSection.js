$(function(){
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		$('.main-content').on('click', '.actions-panel .action-add', function(){
			editContent.open({
				form: '.add-url-section-form',
				clearform: true,
				success: function(res) {
					if (res.errors === null){
                        location.replace(res.data.url);
					} else {
						message.errors(res);
					}
				}
			});
		});
		$('.main-content').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var delName = $(this).data('delname');
			message.confirm({
				text: 'Подтвердите удаление ' + delName + '.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/segment-text/del/',
						type: 'post',
						data: {id: id},
						dataType: 'json',
						success: function(res){
							if (res.errors === null){
								$('.view-content').html(res.content);
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
		
	});
});