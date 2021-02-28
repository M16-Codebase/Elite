$(function() {
	require(['ui', 'poly', 'scrollEvent'], function(ui, Poly, scrollEvent) {
		
		if ( $('.flat-wrap .swiper-container .swiper-slide').length > 1 ) {
			var gallerySwiper = new Swiper ('.flat-wrap .swiper-container', {
				pagination: $('.flat-wrap .swiper-pagination'),
				nextButton: $('.flat-wrap .swiper-button-next'),
				prevButton: $('.flat-wrap .swiper-button-prev'),
				paginationClickable: true,
				slidesPerView: 1
			});
			var sliderIndex = function(swiper){
				var pagin = $('.pagin', swiper);
				var sPagin = $('.swiper-pagination .swiper-pagination-bullet', swiper);
				var activePagin = $('.swiper-pagination .swiper-pagination-bullet-active', swiper);
				pagin.text((activePagin.index()+1) + ' / ' + sPagin.length);
			};
			gallerySwiper.on('onTransitionStart', function () {
				setTimeout(function(){
					sliderIndex('.flat-wrap');
				}, 50);
			});
			sliderIndex('.flat-wrap');
		}
		
		$('.gallery-tiles').each(function() {
			var cont = $(this);
			var galSmall = $('.gallery-small', cont);
			var galBig = $('.gallery-big', cont);
			var gallerySwiper = new Swiper ($('.swiper-container', galBig), {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.swiper-pagination', galBig),
				nextButton: $('.swiper-button-next', galBig),
				prevButton: $('.swiper-button-prev', galBig)
			});
			$('.open-photo', cont).on('click', function() {
				if (!$(this).data('id')) return false;
				var imgId = $(this).data('id');
				var index = galBig.find('.img-' + imgId).closest('.swiper-slide').index();
				gallerySwiper.slideTo(index, 0);
				TweenMax.to(galSmall, 0.4, {
					opacity: 0,
					onComplete: function() {
						galBig.css({left: 0});
						TweenMax.to(galBig, 0.4, {opacity: 1});
					}
				});
				ui.scrollTo(cont, {shift: -50});
				return false;
			});
			$('.close-photo', cont).click(function() {
				var galBig = $(this).closest('.gallery-big');
				TweenMax.to(galBig, 0.4, {
					opacity: 0,
					onComplete: function() {
						galSmall.css({opacity: 0});
						galBig.css({left: '-99999px'});
						TweenMax.to(galSmall, 0.4, {opacity: 1});
						TweenMax.to(cont, 0.4, {height: 'auto'});
					}
				});
				return false;
			});
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