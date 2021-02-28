$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		//добавить список рассылки
		$('.actions-panel .action-button.action-add').click(function() {
			editContent.open({
				form: '.subscribers-lists-form',
				data : $(this).data('group_id'),
				loadform: function() {
				},
				success: function(res) {
					$('.white-body').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			});
			return false;
		});
		
		// удалить список рассылки
		$('.viewport').on('click', '.white-block-row .action-delete', function() {
			var listId = $(this).closest('.white-block-row').data('id');
			message.confirm({
				text: 'Подтвердите удаление баннера.',
				type: 'delete',
				ok: function() {
					$.post('/subscribe/deleteSubscribersList/', {id: listId}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							$('.white-body').html(res.content);
							$(window).resize();
							ui.initAll();
							customSlidebox();
						}
					}, 'json');
				}
			});
			return false;
		});
//	$('.actions-panel .action-add').click(function(){
//		$('.popup-create-list').dialog({
//			title: 'Название'
//		}).dialog('open');
//	});
//	
//	$('.popup-create-list form').submit(function(evt){
//		evt.preventDefault();
//		$(this).ajaxSubmit({
//			dataType: 'json',
//			success: function(res){
//				if (res.errors === null){
//					window.location.replace('/subscribe/subscribers/?group_id=' + res.data.group_id);
//				}
//			}
//		});
//	});
//	
//	$('.subscribers-list').on('click', '.table-btn.delete', function(evt){
//		evt.preventDefault();
//		var btn = $(this);
//		var row = btn.parents('tr');
//		if (confirm('Вы уверены, что хотите удалить "' + row.data('name') +'"?')){
//			$.ajax({
//				url: '/subscribe/deleteSubscribersList/',
//				type: 'post', 
//				data: {id: row.data('id')},
//				dataType: 'json',
//				success: function(res){
//					if (res.errors === null){
//						$('.subscribers-list').html(res.content);
//					}
//				}
//			});
//		}
//	});
//	
//	$('.actions-panel .action-sync A').click(function(evt){
//		evt.preventDefault();
//		$.ajax({
//			url: '/subscribe/synchronize/',
//			success: function(){
//				alert('Синхронизация завершена');
//			}
//		});
//	});
	});
});