$(function(){
    //удаление
    $('.discountList TBODY').delegate('.delete-discount', 'click', function(){
        if (confirm('Удалить акцию?')){
            var id = $(this).closest('TR').data('id');
            $.post('/discount/delete/', {id: id, region_id: $(this).closest('.discountList').data('region_id')}, function(result){
                $('.discountList TBODY').html(result);
				$('H1 .count').text($('.discountList TR').length - 1);
            });
        }
        return false;
    });
	
	// сортировка типов
	$('.ribbed.discountList').sortable({
		handle: '.drag-drop.m-active',
		items: "TR:not(.unchangeable)",
		revert: false,
		stop: function(event, ui) {
			var dcId = parseInt($('INPUT[name="id"]', ui.item).val());
			var oldPosition = parseInt($('INPUT[name="position"]', ui.item).val()); // был номер позиции
			var newPosition = parseInt($('INPUT[name="position"]', ui.item.next()).val()); // номер позиции следующего элемента
			if (isNaN(newPosition) || oldPosition != newPosition - 1) {
				if (isNaN(newPosition) || oldPosition < newPosition) { // если isNaN следующий элемент, значит переместили в конец (следовательно old < new)
					newPosition = parseInt($('INPUT[name="position"]', ui.item.prev()).val());
				}
				$.ajax({
					url : "/discount/move/",
					type : "POST",
					data : {
						id: dcId,
						position: newPosition
					}
				});
			}
		}
	});
	
});