$(function() {
	require(['ui', 'clickOut'], function(ui, clickOut) {		
	
		$('.make-order').click(function() {
			var url = $(this).attr('href');
			if ($('.catalog-item-offer .select-offer:checked').length) {
				url += '?';
				$('.catalog-item-offer .select-offer:checked').each(function(i) {
					if (i) url += '&';
					url += 'id[]=' + $(this).val();
				});
				location.href = url;
			}
			return false;
		});
	
	});
});