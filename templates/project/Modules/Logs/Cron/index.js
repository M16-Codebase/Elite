$(function(){
	$('.errors_count').click(function(){
		var task_id = $(this).closest('.wblock').data('task_id');
		var popup = $('.popup-errors_log');
		$.post('/logs-cron/getErrors/', {task_id: task_id}, function(result){
			popup.html(result).dialog('option', {
				title: 'Ошибки',
				width: 900
			}).dialog('open');
		});
		return false;
	});
    $('.set_event').click(function(){
        $.post('/cron-shedule/' + $(this).data('event') + 'Task/', {task_id: $(this).closest('.wblock').data('task_id')}, function(res){
            if (res.status){
                alert(res.status);
            }
        });
        return false;
    });
});