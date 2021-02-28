$(function () {
	require(['ui'], function(ui) {

	var initSwipe = function() {
			if ($('.swiper .swiper-container .swiper-slide').length > 1) {
				new Swiper ('.swiper .swiper-container', {
					pagination: $('.swiper .swiper-pagination'),
					nextButton: $('.swiper .swiper-button-next'),
					prevButton: $('.swiper .swiper-button-prev'),
					paginationClickable: true,
					slidesPerView: 1
				});
			}
			$('.item-wrap').each(function() {
				var cont = $('.swiper-container', this);
				if ($('.swiper-slide', cont).length > 1) {
					new Swiper (cont, {
						nextButton: $('.swiper-button-next', this),
						prevButton:$('.swiper-button-prev', this),
						slidesPerView: 1
					});
				}
			});
		};
		initSwipe();
		$(".request-form").submit(function(){
			if ($('INPUT[name="phrase"]', this).val() === '') return false;
		});

		
		var result = $('.list-wrap');
		
		result.on('click', '.see-more', function() {
			var btn = $(this);
			var page = $(this).data('page');
			if (!page) return false;
			var url = $(this).data('url')
			var phrase = $(this).data('phrase')
			
			$.get('/main'+url , {phrase: phrase, page: page, ajax: 1}, function(res) {
				var content = $('<DIV />').html(res.content);
				var fullContent = content;
				var toChange = result;
				var newItems = $('.item-wrap', content).css({opacity: 0});
				var seeMore = $('.more-row', fullContent);
				btn.closest('.more-row').replaceWith(newItems);
				newItems.each(function(i) {
					var item = $(this);
					setTimeout(function() {
						TweenMax.to(item, 0.4, {opacity: 1});
					}, i*200);
				});
				$('.more-row', result).replaceWith(seeMore);
//					$('.remove').remove();
//					btn.closest('.list-wrap').append(res.content);
					
//					var changeInner = false;
//					var content = $('<DIV />').html(res.content);
//					var fullContent = content;
//					var toChange = result;
//					var newItems = $('.item-wrap', content).css({opacity: 0});
//					var seeMore = $('.more-row', fullContent);
//					toChange.append(newItems);
//					newItems.each(function(i) {
//						var item = $(this);
//						setTimeout(function() {
//							TweenMax.to(item, 0.4, {opacity: 1});
//						}, i*200);
//					});
//					$('.more-row', result).replaceWith(seeMore);
			}, 'json');
//			
////			submitFilter({page: page});
//			return false;
		});
	});
});