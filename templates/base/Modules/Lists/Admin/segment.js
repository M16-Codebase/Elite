$(function() {
	require(['ui'], function(ui) {		
	
		var editPopap = $('.popup-window-editRegion');
		var addPopap = $('.popup-window-addRegion');
		var popups = $('.popup-region');
		$('.regionList').delegate('.dd-list LI', 'click', function(){
			ui.dropdown.close($(this).closest('.dropdown'));
		});
		
		// открытие попапа для редактирования
		$('.regionList').delegate('.edit-region', 'click', function(){
			$('FORM', editPopap).clearForm();
			var row = $(this).closest('TR');
			$.post('/lists/editFieldsSegment/', {id: row.data('id')}, function(result){
				$('FORM .fields', editPopap).html(result);
				editPopap.dialog('option', {
					'title': 'Редактирование региона'
				}).dialog('open');
			});
			return false;
		});
		
		// открытие попапа для создания
		$('.actions-panel .action-add').click(function(){
			$('FORM', addPopap).clearForm();
			addPopap.dialog('option', {
				'title': 'Создание региона'
			}).dialog('open');
		});
		
		// создание/редактирование
		$('FORM', popups).submit(function(){
			$(this).ajaxSubmit({
                dataType: 'json',
				success: function(result){
                    if (result.errors){

                    } else {
                        $('.regionList TBODY').html(result.content);
                        popups.dialog('close');
                        ui.initAll();
                    }
				}
			});
			return false;
		});
		
		// удаление
		$('.regionList TBODY').delegate('.delete-region', 'click', function(){
			if (confirm('Удалить регион?')){
				var id = $(this).closest('TR').data('id');
				$.post('/lists/delSegment/', {id: id}, function(result){
					$('.regionList TBODY').html(result);
					ui.initAll();
				});
			}
			return false;
		});
		
		// Сбросить время CommerceML
		$('.regionList').delegate('.reset-cml', 'click', function(){
			if (!confirm('Сбросить время последней выгрузки в CommerceML?')) return false;
			var dd = $(this).closest('.dropdown');
			var tr = $(this).closest('TR');
			$.post('/lists/clearLastUpdate/', {
				segment_id: tr.data('id'),
				target: 'exportCommerceML_last_update'
			}, function(result){
				ui.dropdown.close(dd);
				$('.time-cml', tr).text('—');
			});
			return false;
		});
		
		// Сбросить время CSV
		$('.regionList').delegate('.reset-csv', 'click', function(){
			console.log(ui);
			if (!confirm('Сбросить время последней выгрузки в CSV')) return false;
			var dd = $(this).closest('.dropdown');
			var tr = $(this).closest('TR');
			$.post('/lists/clearLastUpdate/', {
				segment_id: tr.data('id'),
				target: 'csv_last_update'
			}, function(result){
				ui.dropdown.close(dd);
				$('.time-csv', tr).text('—');
			});
			return false;
		});

	});
});