$(function(){
    $('.action-button.action-add').click(function(){
        $('.popup-window-addManuf').dialog('open');
    });
    $('.addCover, .reloadCover').click(function(){
        var row = $(this).closest('TR');
        $('INPUT[name="manuf_key"]', $('.popup-window-addManufCover')).val(row.data('manuf_key'));
        $('.popup-window-addManufCover FORM INPUT[name="cover"]').val('');
        $('.popup-window-addManufCover').dialog({
			title: 'Загрузить обложку',
			width: 600
		}).dialog('open');
        return false;
    });
    
    $('.popup-window-addManufCover FORM').ajaxForm({
        dataType: 'json',
        success: function(result, xhr, status, form){
			if (result.error) {
				$('.popup-errors .error-text').html(result.error);
				$('.popup-errors').dialog({
					title: 'Ошибка'
				}).dialog('open');
			} else if (result.url){
                $('.cover-input-' + $('INPUT[name="manuf_key"]', form).val()).html('<img src="'+result.url+'?'+new Date()+'" class="reloadCover" />');
            }
            $('.popup-window-addManufCover').dialog('close');
        }
    });
    
    $('.addFile, .reloadFile').click(function(){
		var btn = $(this);
		var reload = btn.hasClass('reloadFile');
        var row = btn.closest('TR');
		if (reload){
			$('.popup-window-addManufFile .remove-file-btn').show().data('manuf_key', row.data('manuf_key')).click(function(evt){
				evt.preventDefault();
				var remLink = $(this);
				$.ajax({
					url: remLink.attr('href'),
					data: {
						manuf_key: row.data('manuf_key')
					},
					type: 'post',
					dataType: 'json',
					success: function(res){
						btn.children().removeClass('reload').addClass('add');
						remLink.hide();
						$('.popup-window-addManufFile').dialog('close');
					}
				});
			}).show();
		} else {
			 $('.popup-window-addManufFile .remove-file-btn').hide();
		}
        $('INPUT[name="manuf_key"]', $('.popup-window-addManufFile')).val(row.data('manuf_key'));
        $('.popup-window-addManufFile').dialog({
			title: 'Загрузить файл',
			width: 600
		}).dialog('open');
        return false;
    });
    
    $('.popup-window-addManufFile FORM').ajaxForm({
		dataType: 'json',
        success: function(result, xhr, status, form){
			if (result.error) {
				$('.popup-errors .error-text').html(result.error);
				$('.popup-errors').dialog({
					title: 'Ошибка'
				}).dialog('open');
			} else {
				$('tr[data-manuf_key="' + $('INPUT[name="manuf_key"]', form).val() + '"] .reloadFile > div').removeClass('add').addClass('reload');
            }
			$('.popup-window-addManufFile').dialog('close');
        }
    });
    
	// Удалить производителя
	$('.manuf_row .delete').click(function() {
		if (!confirm('Удалить производителя')) return false;
		var key = $(this).data('key');
		$.post('/manuf/del/', {key: key}, function(res) {
			if (!res.status) return false;
			else location.reload();
		}, 'json');
	});
	
	// Удалить выбранных производителей
	$('.actions-panel .action-delete').click(function() {
		if ($(this).hasClass('m-inactive')) return false;
		if (!confirm('Удалить выбранных производителей')) return false;
		$('.manuf-table').wrap('<form action="/manuf/del/" method="POST" class="manuf-del-form"></form>');
		$('.manuf-del-form').ajaxSubmit({
			dataType: 'json',
			success: function(res) {
				if (!res.status) return false;
				else location.reload();
			}
		});
		return false;
	});
	
    // Сортировка производителей
    $('TABLE.ribbed TBODY').sortable({			
        handle: '.drag-drop',
        items: "TR",
        revert: false,
        stop: function(event, ui) {			
            var manuf_key = ui.item.data('manuf_key');
            var oldPosition = ui.item.data('position'); // был номер позиции
            var newPosition = ui.item.next().data('position'); // номер позиции следующего элемента
            if (oldPosition != newPosition - 1) {
                if (isNaN(newPosition) || oldPosition < newPosition) { // если isNaN следующий элемент, значит переместили в конец (следовательно old < new)
                    newPosition = ui.item.prev().data('position');
                }
                $.getJSON("/manuf/changePosition/",
                    {
                        manuf_key: manuf_key,
                        position: newPosition
                    },
                    function(result){
                        if (result.status && result.status == 'ok'){
                            $('.manuf_row').each(function(){
                                if ($(this).attr('data-manuf_key') == manuf_key){
                                    $(this).attr('data-position', newPosition);
                                } else if (newPosition < oldPosition && $(this).attr('data-position') >= newPosition && $(this).attr('data-position') < oldPosition){
                                    $(this).attr('data-position', parseInt($(this).attr('data-position')) + 1);
                                } else if (newPosition > oldPosition && $(this).attr('data-position') <= newPosition && $(this).attr('data-position') > oldPosition){
                                    $(this).attr('data-position', parseInt($(this).attr('data-position')) - 1);
                                }
                            });
                            $('.ribbed TBODY').html($('.ribbed TBODY').html());
                        }
                    }
                );
            }
        }
    });
});