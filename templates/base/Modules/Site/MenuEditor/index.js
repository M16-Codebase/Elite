$(function() {	
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {

		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-menu-form',
				clearform: true,
				success: function(res) {
					$('.menu-list').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initall();
				}
			});
		});

		$('.menu-list').on('click', '.action-edit', function(){
			var key = $(this).data('key');
			var id = $(this).data('id');
			editContent.open({
				form: '.edit-menu-form',
				clearform: true,
				success: function(res) {
					$('.menu-list').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initall();
				}
			});
			$('.edit-menu-id').val(id);
			$('.edit-key').val(key);
			return false;
		});

		$('.menu-list').on('click', '.action-delete', function() {
			var id = $(this).data('id');
			message.confirm({
				text: 'Подтвердите удаление меню.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/menu-editor/deleteMenu/',
						type: 'post',
						data: {id: id},
						dataType: 'json',
						success: function(res) {
							if (res.errors) {
								message.errors(res);
							} else {
								$('.menu-list').html(res.content);
								$(window).resize();
								ui.initall();
							}
						},
						error: function(err) {
							message.serverErrors(err);
						}
					});
				}
			});
			return false;
		});
		
	});
});