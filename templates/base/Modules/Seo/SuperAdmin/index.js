$(function(){
	require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {
		
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-meta-form',
				clearform: true,
				 dataType:'json',
				success: function(res) {
					if (res.errors === null){
						window.location = res.data.url;
					} else {
						message.errors(res);
					}
				}
			});
		});

		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var name = $(this).closest('.white-block-row').data('name');
			message.confirm({
				text: 'Подтвердите удаление SEO настройки для ' + name + '.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/seo/?del='+ id,
						type: 'post',
						dataType: 'json',
						success: function(res){
							if (res.errors === null){
								$('.white-body').html(res.content);
								$(window).resize();
							} else {
								message.errors(res);
							}
						}
					});
				}
			});
			return false;
		});
		
		$('.white-body').on('click', '.action-visibility', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var enabled = $(this).hasClass('action-show');
			$.ajax({
				url: '/seo/?id='+ id + '&enabled=' + (enabled? 0:1),
				type: 'post',
				dataType: 'json',
				success: function(res){
					if (res.errors === null){
						$('.white-body').html(res.content);
						$(window).resize();
					} else {
						message.errors(res);
					}
				}
			});
			return false;
		});
	});
});