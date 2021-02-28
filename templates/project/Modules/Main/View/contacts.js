$(function() {
	require(['ui', 'mapstyles'], function(ui, mapstyles) {
		
		
		$('.consultant').each(function(i){
			var cont = $(this);
			if ($('.swiper-slide',cont).length > 4) {
				new Swiper (cont, {
					slidesPerView: $('.swiper-slide').length,
					nextButton: $('.swiper-button-next', this),
					prevButton:$('.swiper-button-prev', this),
				});
			}
		});
		
//		$('.consultant').each(function(i){
//			var cont = $(this);
//				new Swiper (cont, {
//					slidesPerView: $('.swiper-slide').length,
//					nextButton: $('.swiper-button-next', this),
//					prevButton:$('.swiper-button-prev', this),
//					onInit: function(e){
//						if ($('.swiper-slide',cont).length < 1) {
//							e.lockSwipes();
//						}
//					}
//				});
//		});
		
	});
});