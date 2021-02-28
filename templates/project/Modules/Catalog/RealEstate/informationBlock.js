$(function () {
	require(['ui', 'scrollEvent'], function(ui, scrollEvent) {
		
		if ( $('.flat-wrap .swiper-container .swiper-slide').length > 1) {
			var gallerySwiper = new Swiper ('.flat-wrap .swiper-container', {
				slidesPerView: 1,
				paginationClickable: true,
				spaceBetween: 2,
				pagination: $('.flat-wrap .swiper-pagination'),
				nextButton: $('.flat-wrap .swiper-button-next'),
				prevButton: $('.flat-wrap .swiper-button-prev')
			});
			var after = $('.swiper-container-after');
			var showTime = 400;

			var sliderIndex = function(swiper){
				var pagin = $('.pagin', swiper);
				var sPagin = $('.swiper-pagination .swiper-pagination-bullet', swiper);
				var activePagin = $('.swiper-pagination .swiper-pagination-bullet-active', swiper);
				pagin.text((activePagin.index()+1) + ' / ' + sPagin.length);
			};
			gallerySwiper.on('onTransitionStart', function () {
				var activeSlide = $('.flat-wrap .swiper-container .swiper-slide.swiper-slide-active');
				var params = $('.params', activeSlide).html();

				$('.params', after).animate({opacity: 0}, showTime);
				setTimeout(function(){
					$('.params', after).html(params);
				}, showTime);
				setTimeout(function(){
					sliderIndex('.flat-wrap');
				},50);
			});

			gallerySwiper.on('onTransitionEnd', function () {
				$('.params', after).animate({
					opacity: 1
				}, showTime);
			});
			sliderIndex('.flat-wrap');
		};
		$(".gallery A").fancybox();
		
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