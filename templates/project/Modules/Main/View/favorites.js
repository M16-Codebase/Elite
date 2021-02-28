$(function() {
	require(['ui'], function(ui) {
		
		var favCont = $('.favorites');
		var check = function() {
			var items = $('.fav-item INPUT:checkbox', favCont);
			var sort = $('.fav-sort INPUT:checkbox', favCont);
			if (items.length === items.filter(':checked').length) {
				sort.prop('checked', true).trigger('ui-check-update');
			} else {
				sort.prop('checked', false).trigger('ui-check-update');
			}
			if (items.filter(':checked').length) {
				var url = $('a.make-order').data('href');//'/favorites_request/';
				items.filter(':checked').each(function(i) {
					url += (i? '&' : '?') + 'id[]=' + $(this).val();
				});
				$('.fav-order .make-order', favCont).attr('href', url);
				$('.fav-order .num', favCont).text(items.filter(':checked').length);
				$('.fav-sort', favCont).fadeOut(300, function() {
					$('.fav-order', favCont).fadeIn(300);
				});
			} else {
				$('.fav-order', favCont).fadeOut(300, function() {
					$('.fav-sort', favCont).fadeIn(300);
				});
			}
		};
		favCont.on('change', '.fav-sort INPUT:checkbox', function() {
			var items = $('.fav-item', favCont);
			var cbx = $('.fav-item INPUT:checkbox', favCont);
			if ($(this).is(':checked')) {
				cbx.prop('checked', true).trigger('ui-check-update');
				items.addClass('m-active');
			} else {
				cbx.prop('checked', false).trigger('ui-check-update');
				items.removeClass('m-active');
			}
			check();
		});
		favCont.on('change', '.fav-item INPUT:checkbox', function() {
			var item = $(this).closest('.fav-item');
			if ($(this).is(':checked')) {
				item.addClass('m-active');
			} else {
				item.removeClass('m-active');
			}
			check();
		});
		favCont.on('click', '.unselect', function() {
			var items = $('.fav-item', favCont);
			var cbx = $('.fav-item INPUT:checkbox', favCont);
			cbx.prop('checked', false).trigger('ui-check-update');
			items.removeClass('m-active');
			check();
			return false;
		});
		favCont.on('click', '.fav-item .f-del', function() {
			if (!confirm('Удалить квартиру из списка избранных предложений?')) return false;
			var item = $(this).closest('.fav-item');
			var id = $(this).data('id');
			var url = $(this).data('url');
			$.post(url + '/removeFromFavorites/?ajax=1', {entity_id: id}, function(res) {
				item.fadeOut(400, function() {
					if (res.content) favCont.html(res.content);
					ui.initAll();
					check();
				});
			}, 'json');
			return false;
		});
		favCont.on('click', '.fav-sort .f-del', function() {
			if (!confirm('Очистить список избранных предложений?')) return false;
			var id = $(this).data('id');
			$.post($(this).data('href'), function(res) {
				favCont.fadeOut(400, function() {
					if (res.content) favCont.html(res.content);
					favCont.fadeIn(400);
					ui.initAll();
				});
			}, 'json');
			return false;
		});
		
		
	});
});