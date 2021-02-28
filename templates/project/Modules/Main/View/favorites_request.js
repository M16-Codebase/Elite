$(function() {
	require(['ui'], function(ui) {
		
		if ( $('.swiper-container .swiper-slide').length > 1 ) {
			var swiperCont = $('.request-form.m-swiper .swiper-container');
			var gallerySwiper = new Swiper (swiperCont, {
				pagination: $('.swiper-pagination', swiperCont),
				nextButton: $('.swiper-button-next', swiperCont),
				prevButton: $('.swiper-button-prev', swiperCont),
				paginationClickable: true,
				slidesPerView: 1
			});
			var sliderIndex = function(swiper){
				var pagin = $('.pagin', swiper);
				var liCount = $('.swiper-slide LI', swiper).length;
				var activeSlide = $('.swiper-slide-active', swiper);
				var currentLi = $('LI', activeSlide).index('.swiper-slide LI');
				var lustLi = $('LI:last', activeSlide).index('.swiper-slide LI');
				
				pagin.html('<span>' + (currentLi+1) + ' — ' + (lustLi+1) + '</span> <i>/</i> ' + liCount);
			};
			gallerySwiper.on('onTransitionStart', function () {
				setTimeout(function(){
					sliderIndex(swiperCont);
				}, 50);
			});
			sliderIndex(swiperCont);
		}
		
	});
});