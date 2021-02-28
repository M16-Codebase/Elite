$(function() {
		
	// show more
	$('.more-btn').each(function() {
		var closed = true;
		var btn = $('.btn', this);
		var closedText = btn.text();
		var openText = btn.data('alt-text');
		var item = $('.promo-block.more-item');
		btn.click(function() {
			if (closed) {
				btn.text(openText);
				item.stop().slideDown();
			} else {
				btn.text(closedText);
				item.stop().slideUp();
			}
			closed = !closed;
			return false;
		});
	});
		
});