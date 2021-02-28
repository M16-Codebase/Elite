$(function() {
	
	$('.promo-image').each(function() {
		var item = $(this);
		var img = $('IMG', this);
		var imgW = img.width();
		var checkSizes = function() {
			var contW = item.width();
			if (imgW < contW*0.9) {
				checkSizes();
			} else {
				img.css({
					left: (contW - imgW)/2
				});
			}					
		};
		$(window).on('load resize', checkSizes);
		checkSizes();
	});
	
});