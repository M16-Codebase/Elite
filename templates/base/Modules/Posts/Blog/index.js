$(function() {
		
	// удаление тегов в поиске
	$('.search-form').each(function() {
		var btnRemove = $('.btn-remove', this);
		var input = $('INPUT', this);
		btnRemove.click(function() {
			input.val('');
			return false;
		});
	});
	
	
});