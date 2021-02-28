$(function() {
	require(['ui', 'scrollEvent'], function(ui, scrollEvent) {
		
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
		
		//fixed main-menu
		if ( $('.main-menu').length ) {
			scrollEvent({
				'.page-header': {
					start: 0,
					outActive: function() {
						$('.main-menu').addClass('m-fixed');
						console.log('bom bom')
					},
					onActive: function() {
						$('.main-menu').removeClass('m-fixed');
					}
				}
			});		
		}
		
	});
});