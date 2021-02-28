$(function() {
	require(['catalog-item'], function(initCatalogItem) {
		var firstload = true;
		var type_id = $('.verticalList').data('type_ids');
		var page_size = Math.ceil($('.edited-text').outerHeight() / 440);
		if (page_size < 1) page_size = 1;
		if (page_size > 5) page_size = 5;
		var getItems = function(page) {
			page = page || 1;
			$.post('/catalog/verticalList/', {
				type_id: type_id, 
				page_size: page_size,
				page: page
			}, function(result){
				if ($('.verticalList .aside-items').length) {
					var newItems = $('<div>').html(result); 
					$('.aside-items', newItems).css({opacity: 0});
					$('.verticalList .aside-items').animate({opacity: 0}, 300, function() {
						$('.verticalList').html(newItems.html());
						$('.verticalList .aside-items').animate({opacity: 1}, 300, initCatalogItem);
					});	
				} else {
					$('.verticalList').html(result);
					initCatalogItem();
				}	
				if (!firstload) {
					$('HTML, BODY').stop(true, true).animate({
						scrollTop: $('.aside-items').offset().top - 42
					}, 400);
				}
				firstload = false;
			});
		}
		$('.verticalList').delegate('.paging .pages A', 'click', function() {
			getItems($(this).text());
			return false;
		}).delegate('.paging .prev', 'click', function() {
			var num = parseInt($('.paging .pages .m-current A').text()) - 1;
			getItems(num);
			return false;
		}).delegate('.paging .next', 'click', function() {
			var num = parseInt($('.paging .pages .m-current A').text()) + 1;
			getItems(num);
			return false;
		});
		getItems();
		
		// Ещё по теме
		(function() {
			if (!$('.float-articles').length) return;
			var floatBlock = $('.float-articles');
			
			var closed = false;
			$('.close', floatBlock).bind('click', function() {
				floatBlock.fadeOut(200);
				closed = true;
			});
			
			var bottomLine = $('.bottom-line');
			var floatheight = floatBlock.outerHeight(true);
			$(window).bind('scroll resize touchmove MSPointerMove', function() {
				if (closed) return;
				var top = $(window).scrollTop() + $(window).height() - floatheight;
				var bottom = bottomLine.offset().top - floatheight;
				if (top >= bottom) {
					floatBlock.removeClass('m-fixed');
				} else {
					floatBlock.addClass('m-fixed');
				}
			});
		}());		
		
		
	});    
});