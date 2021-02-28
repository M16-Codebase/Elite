$(function(){
	$('.errors_count').click(function(){
		var task_id = $(this).closest('TR').data('task_id');
		var popup = $('.popup-errors_log');
		$.post('/exchange/importErrors/', {task_id: task_id}, function(result){
			popup.html(result).dialog('option', {title: 'Ошибки'}).dialog('open');
		});
	});
});