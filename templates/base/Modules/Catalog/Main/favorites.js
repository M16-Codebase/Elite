$(function() {
	require([], function() {
		
		// check all
		var allCbx = $('.selected-header .check-all');
		var cbxs = $('.catalog-item-offer .select-offer');
		allCbx.click(function() {
			var on = $(this).is(':checked');
			cbxs.prop('checked', on).change();
		});
		cbxs.click(function() {
			var all = (cbxs.length === cbxs.filter(':checked').length);			
			allCbx.prop('checked', all).change();
		});
		
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

