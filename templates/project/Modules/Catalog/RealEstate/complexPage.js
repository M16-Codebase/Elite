$(function() {
	require(['ui', 'tiles', 'mapstyles', 'scrollEvent'], function(ui, tiles, mapstyles, scrollEvent) {
		
		var mobileDetect = $(".mobile-detect");

		//if ($('.main-menu .swiper-container').length) {
		if ($('.main-menu .swiper-container').length && $(window).width() > 767) {
			//menu swiper
			var menuSwiper = new Swiper ('.main-menu .swiper-container', {
				freeMode: true,
				scrollbarHide: false,
				slidesPerView: 'auto',
				scrollbar: '.main-menu .swiper-container .swiper-scrollbar'
			});
			var changeMobWidth = 'a';
			var swiperDstr = function(){
				if (mobileDetect.width() != changeMobWidth){
					if (!mobileDetect.width()) {
						menuSwiper.lockSwipes();
					} else {
						menuSwiper.update();
						menuSwiper.unlockSwipes();
					} 
					changeMobWidth = mobileDetect.width();
				}
			};
			swiperDstr();
			$(window).resize(function(){
				swiperDstr();
			});
		}

		if($(window).width() > 767){
			$('.swipe-wrap').each(function(i){
				var cont = $('.swiper-container', this);
				if ($('.swiper-slide',cont).length > 2) {
					new Swiper (cont, {
						spaceBetween: 32,
						slidesPerView: 2,
						slidesPerGroup: 2,
						nextButton: $('.swiper-button-next', this),
						prevButton:$('.swiper-button-prev', this)
					});
				}
			});
		}
		else {
			$('.swipe-wrap').each(function(i){
				var cont = $('.swiper-container', this);
				if ($('.swiper-slide',cont).length > 2) {
					new Swiper (cont, {
						spaceBetween: 0,
						slidesPerView: 1,
						slidesPerGroup: 1,
						nextButton: $('.swiper-button-next', this),
						prevButton:$('.swiper-button-prev', this)
					});
				}
			});
		}



		/*$('.swipe-wrap').each(function(i){
			var cont = $('.swiper-container', this);
			if ($('.swiper-slide',cont).length > 2) {
				new Swiper (cont, {
					spaceBetween: 32,
					slidesPerView: 2,
					slidesPerGroup: 2,
					nextButton: $('.swiper-button-next', this),
					prevButton:$('.swiper-button-prev', this)
				});
			}
		});*/
		
		// галерея
		$('.gallery-tiles').each(function() {
			var cont = $(this);
			var galBig = $('.gallery-big', cont);
			var galSmall = $('.gallery-small', cont);
			$('.open-photo', cont).click(function() {
				var rows = $('.tiles-inner', cont).data('rows')/2;
				var imgId = $(this).data('id');
				TweenMax.to(galSmall, 0.4, {
					opacity: 0,
					onComplete: function() {
						galBig.css({left: 0});
						TweenMax.to(galBig, 0.4, {opacity: 1});
					}
				});
				TweenMax.to(cont, 0.4, {height: (rows < 2)? cont.height()*2 : 'auto'});
				if (imgId) {
					var index = $('.gallery-big .swiper-container', cont).find('.img-' + imgId).closest('.swiper-slide').index();
					gallerySwiper.slideTo(index,0);
				}
				ui.scrollTo(cont, {shift: -76});
				return false;
			});
			var gallerySwiper = new Swiper ('.gallery-big .swiper-container', {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.gallery-big .swiper-pagination'),
				nextButton: $('.gallery-big .swiper-button-next'),
				prevButton:$('.gallery-big .swiper-button-prev'),
			});
			$('.close-photo', cont).click(function() {
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
			
			var tilesPos = {
				max: {
					2: {
						'info': [7,1, 8,2],
						'photo-1': [1,1, 2,2],
						'photo-2': [3,1, 6,2]
					},
					3: {
						'info': [7,1, 8,2],
						'photo-1': [1,1, 2,1],
						'photo-2': [1,2, 2,2],
						'photo-3': [3,1, 6,2]
					},
					4: {
						'info': [3,3, 4,4],
						'photo-1': [1,1, 4,2],
						'photo-2': [5,1, 8,2],
						'photo-3': [1,3, 2,4],
						'photo-4': [5,3, 8,4]
					},
					5: {
						'info': [3,3, 4,4],
						'photo-1': [1,1, 2,2],
						'photo-2': [3,1, 6,2],
						'photo-3': [7,1, 8,2],
						'photo-4': [1,3, 2,4],
						'photo-5': [5,3, 8,4]
					},
					6: {
						'info': [3,3, 4,4],
						'photo-1': [1,1, 2,1],
						'photo-2': [1,2, 2,2],
						'photo-3': [3,1, 6,2],
						'photo-4': [7,1, 8,2],
						'photo-5': [1,3, 2,4],
						'photo-6': [5,3, 8,4]
					},
					7: {
						'info': [3,3, 4,4],
						'photo-1': [1,1, 2,1],
						'photo-2': [1,2, 2,2],
						'photo-3': [3,1, 6,2],
						'photo-4': [7,1, 8,1],
						'photo-5': [7,2, 8,2],
						'photo-6': [1,3, 2,4],
						'photo-7': [5,3, 8,4]
					}
				}
			};
			var tilesFormat = function(resp) {
				var t = [];
				var photoCount = $('.photo-cont', galSmall).length;
				if (photoCount > 7) photoCount = 7;
				if (!resp) resp = 'max';
				if (!tilesPos[resp] || !tilesPos[resp][photoCount]) return;
				for (var i in tilesPos[resp][photoCount]) {
					t.push({
						el: $('.tiles .' + i, cont),
						pos: tilesPos[resp][photoCount][i]
					});
				};
				return t;
			};
			tiles($('.tiles', this), {
				tiles: tilesFormat(),
				size: [50, 900],
				space: 3,
				cols: 8
			});
		});
		
		// статьи
		$('.art-tiles').each(function() {
			var cont = $(this);
			var tilesPos = {
				max: {
					1: {
						'art-info': [[1,1]],
						'art-cover': [[2,1]],
						'art-1': [[3,1, 4,1], 'm-black']
					},
					2: {
						'art-info': [[2,2]],
						'art-cover': [[2,1]],
						'art-1': [[1,1, 1,2], 'm-white m-rotated'],
						'art-2': [[3,1, 4,2], 'm-black']
					},
					3: {
						'art-info': [[2,2]],
						'art-cover': [[2,1]],
						'art-1': [[1,1, 1,2], 'm-white m-rotated'],
						'art-2': [[3,1, 4,1], 'm-orange'],
						'art-3': [[3,2, 4,2], 'm-black']
					},
					4: {
						'art-info': [[2,2]],
						'art-cover': [[2,1]],
						'art-1': [[1,1, 1,2], 'm-white m-rotated'],
						'art-2': [[3,1, 4,2], 'm-black'],
						'art-3': [[1,3, 2,4], 'm-black'],
						'art-4': [[3,3, 4,4], 'm-orange']
					},
					5: {
						'art-info': [[2,2]],
						'art-cover': [[2,1]],
						'art-1': [[1,1, 1,2], 'm-white m-rotated'],
						'art-2': [[3,1, 4,1], 'm-white'],
						'art-3': [[3,2, 4,2], 'm-black'],
						'art-4': [[1,3, 2,4], 'm-black'],
						'art-5': [[3,3, 4,4], 'm-orange']
					},
					6: {
						'art-info': [[2,2]],
						'art-cover': [[2,1]],
						'art-1': [[1,1, 1,2], 'm-white m-rotated'],
						'art-2': [[3,1, 4,1], 'm-white'],
						'art-3': [[3,2, 4,2], 'm-black'],
						'art-4': [[1,3, 2,4], 'm-black'],
						'art-5': [[3,3, 3,4], 'm-orange'],
						'art-6': [[4,3, 4,4], 'm-white m-rotated']
					}
				}
			};
			var tilesFormat = function(resp) {
				var t = [];
				var artCount = $('.art-tile', cont).length;
				if (artCount > 6) artCount = 6;
				if (!resp) resp = 'max';
				if (!tilesPos[resp] || !tilesPos[resp][artCount]) return;
				for (var i in tilesPos[resp][artCount]) {
					var el = $('.' + i, cont);
					if (tilesPos[resp][artCount][i][1]) {
						el.attr('class', $(this).data('class'));
						if (!el.hasClass('m-progress')) {
							el.addClass(tilesPos[resp][artCount][i][1]);
						}
					}
					t.push({
						el: el,
						pos: tilesPos[resp][artCount][i][0]
					});
				};
				return t;
			};
			$('.art-tile', cont).each(function() {
				$(this).data('class', $(this).attr('class'));
			});
			tiles(cont, {
				tiles: tilesFormat(),
				size: [50, 900],
				space: 0,
				cols: 4
			});
		});
		
		
		//fixed main-menu
		if ( $('.main-menu').length ) {
			scrollEvent({
				'.top-bg': {
					start: 0,
					outActive: function() {
						$('.main-menu').addClass('m-fixed');
						console.log('bom bom')
					},
					onActive: function() {
						$('.main-menu').removeClass('m-fixed');
					}
				},
			});		
		}
		
	});
});