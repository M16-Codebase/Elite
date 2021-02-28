$(function() {	
	$('.actions-panel .action-visability A').click(function() {
		return false;
	});
	
	// галерея
	(function() {
		if (!$('.view-gallery.carousel').length) return;
		var car = $('.view-gallery.carousel');
		var all = $('.car-wrap LI', car).length;
		var fromText = $('.count .from', car);
		var toText = $('.count .to', car);
		var from = 1;
		$('.car-prev', car).click(function() {
			if ($(this).hasClass('m-inactive')) return false;
			var newFrom = from - 3;
			var newTo = newFrom + 2;
			if (newFrom < 1) {
				newFrom = 1;				
				newTo = newFrom + 2;
			}
			fromText.text(newFrom);
			toText.text(newTo);
			from = newFrom;
		});
		$('.car-next', car).click(function() {
			if ($(this).hasClass('m-inactive')) return false;
			var newFrom = from + 3;
			var newTo = newFrom + 2;
			if (newFrom > (all - 3)) {				
				newTo = all;
				newFrom = newTo - 2;
			}
			fromText.text(newFrom);
			toText.text(newTo);
			from = newFrom;
		});
	})();
	
	// варианты
	$('.variant-item').each(function() {
		var item = $(this);
		var body = $('.body', item);
		var header = $('.header', item);
		var open = item.hasClass('m-open');
		if (open) body.css({display: 'block'});
		else body.hide();
		header.click(function(e) {
			if ($(e.target).hasClass('edit-icon')) return;
			if (open) {
				body.stop(true, true).slideUp(function() {
					item.removeClass('m-open');
				});				
			} else {
				body.stop(true, true).slideDown();
				item.addClass('m-open');
			}
			open = !open;
		});
	});

	// цены и наличие
	$('.price-table').click(function() {
		var id = $(this).closest('.variant-item').data('id');
		$('.popup-viewer-price TR').removeClass('m-current');
		$('.popup-viewer-price TR.var-' + id).addClass('m-current');
		$('.popup-viewer-price').dialog('open');
	});
	$('.avail-table').click(function() {
		var id = $(this).closest('.variant-item').data('id');
		$('.popup-viewer-count TR').removeClass('m-current');
		$('.popup-viewer-count TR.var-' + id).addClass('m-current');
		$('.popup-viewer-count').dialog('open');
	});
});