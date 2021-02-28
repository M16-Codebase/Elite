$(function() {
	require(['ui'], function(ui) {
		
		$('.consultant').each(function(i){
			var cont = $(this);
			if ($('.swiper-slide',cont).length > 1) {
				new Swiper (cont, {
					slidesPerView: 1,
					nextButton: $('.swiper-button-next', this),
					prevButton:$('.swiper-button-prev', this)
				});
			}
		});
		
	});
});