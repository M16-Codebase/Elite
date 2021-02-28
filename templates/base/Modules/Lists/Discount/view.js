$(function() {
	require(['ui'], function(ui) {
		
		var updateList = function(data) {
			data = data || {};
			data.id = $('.promo-page').data('id');
			if ($('.main-catalog-types .catalog-item.m-current').length) {
				data.type_id = $('.main-catalog-types .catalog-item.m-current').data('id');
			}
			$.get('/catalog/itemsList/', data, function(res) {
				if (window.history) {
					history.replaceState({}, '', '?' + $.param(data));
				}
				$('.catalog-block').stop().fadeOut(function() {
					$('.catalog-block').html(res).fadeIn();
					ui.initAll();
					initSort();
				});
			});
		};
		
		// выбор типа	
		$('.main-catalog-types .catalog-item').click(function() {
			if ($(this).hasClass('m-current')) return false;
			$(this).addClass('m-current').siblings().removeClass('m-current');
			updateList();
			return false;
		});
		$('.main-catalog-types .catalog-item .remove-type').click(function() {			
			$(this).closest('.vm-item').removeClass('m-current');
			updateList();
			return false;
		});
			
		// размер типов
		var maxHeight = 0;
		$('.main-catalog-types .catalog-item').each(function() {
			var h = $(this).height();
			if (h > maxHeight) maxHeight = h;
		}).height(maxHeight);
	
		// сортировка
		function initSort() {
			$('.sort-link').click(function() {
				var data = {};
				data[$(this).data('sort')] = $(this).data('val');
				updateList(data);
				return false;
			});
			$('.paging .pages A').click(function() {
				var data = {};
				data['page'] = $(this).text();
				updateList(data);
				return false;
			});
			$('.paging .pg-arrow').click(function() {
				var paging = $(this).closest('.paging');
				var next = $(this).hasClass('a-right')? $('li.m-current', paging).next() : $('li.m-current', paging).prev();
				var data = {
					page: next.text()
				};
				updateList(data);
				return false;
			});
		};
		initSort();
		
	});
});