$(function() {
	require(['ui'], function(ui) {
		
		if ( $('#flashContent').length) {
			if (swfobject.hasFlashPlayerVersion("10.0.0") && $('#flashContent').data('swf')) {
				var swf = $('#flashContent').data('swf');
				var flashvars = {};
				var params = {};
				params.quality = "high";
				params.bgcolor = "#ffffff";
				params.allowscriptaccess = "sameDomain";
				params.allowfullscreen = "true";
				params.base="/";
				var attributes = {};
				attributes.id = "pano";
				attributes.name = "pano";
				attributes.align = "middle";
				swfobject.embedSWF(
					swf , "flashContent",
					"100%", "100%",
					"9.0.0", "expressInstall.swf",
					flashvars, params, attributes);
				//	var xml = $('#flashContent').data('xml');
				//var path = xml.replace(/\/[a-z,0-9,-,_]*\.xml/i,'')
				//	// create the panorama player with the container
				//	pano=new pano2vrPlayer("flashContent", path);
				//	// add the skin object
				//	skin=new pano2vrSkin(pano, path+'/');
				//	// load the configuration
				//	pano.readConfigUrl(xml);
				//	// hide the URL bar on the iPhone
				//	setTimeout(function() { hideUrlBar(); }, 10);
			} else
			// check for CSS3 3D transformations and WebGL
			if ((ggHasHtml5Css3D() || ggHasWebGL()) && $('#flashContent').data('xml')) {
				var xml = $('#flashContent').data('xml');
				var path = xml.replace(/\/[a-z,0-9,-,_]*\.xml/i,'')
				// create the panorama player with the container
				pano=new pano2vrPlayer("flashContent", path);
				// add the skin object
				skin=new pano2vrSkin(pano, path+'/');
				// load the configuration
				pano.readConfigUrl(xml);
				// hide the URL bar on the iPhone
				setTimeout(function() { hideUrlBar(); }, 10);
			}
		}

		//function flashVersion() {
		//	// Отдельно определяем Internet Explorer
		//	var ua = navigator.userAgent.toLowerCase();
		//	var isIE = (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1 && ua.indexOf("webtv") == -1);
		//	// Стартовые переменные
		//	var version = 0;
		//	var newversion;
		//	var lastVersion = 10; // c запасом
		//	var i;
		//	var plugin;
        //
		//	if (isIE) { // browser == IE
		//		try {
		//			for (i = 3; i <= lastVersion; i++) {
		//				if (eval('new ActiveXObject("ShockwaveFlash.ShockwaveFlash.'+i+'")')) {
		//					version = i;
		//				}
		//			}
		//		} catch(e) {}
		//	} else { // browser != IE
		//		for (i in navigator.plugins) {
		//			plugin = navigator.plugins[i];
		//			if (plugin.name == undefined) continue;
		//			if (plugin.name.indexOf('Flash') > -1) {
		//				newversion = /\d+/.exec(plugin.description);
		//				if (newversion == null) newversion = 0;
		//				if (newversion> version) version = newversion;
		//			}
		//		}
		//	}
		//	return version;
		//}
		//console.log(flashVersion());

		if ($('.video-wrap').length) {
			var player = {};
			var initVideo = function() {
				var video = $('.video-gallery');
				var videoCont = video.closest('.swiper-container');
				player = new YT.Player(video[0], {
					videoId: video.data('id'),
					height: videoCont.height()-26,
					width: videoCont.width()
				});
				$(window).on('resize', function() {
					player.setSize(videoCont.width(), videoCont.height()-26);
				});
			};
			(function() {
				var tag = document.createElement('script');
				tag.src = "https://www.youtube.com/iframe_api";
				window.onYouTubeIframeAPIReady = initVideo;
				var firstScriptTag = document.getElementsByTagName('script')[0];
				firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			})();
		}
		
		var sliderIndex = function(swiper) {
			var pagin = $('.pagin', swiper);
			var sPagin = $('.swiper-pagination .swiper-pagination-bullet', swiper);
			var activePagin = $('.swiper-pagination .swiper-pagination-bullet-active', swiper);
			pagin.text((activePagin.index()+1) + ' / ' + sPagin.length);
		};
		if ($('.gallery-bot.swiper-container').length) {
			var previewSwiper = new Swiper ('.gallery-thumbs.swiper-container', {
				slidesPerView: 7,
				paginationClickable: true,
				slideToClickedSlide: true,
				spaceBetween: 10
			});

			var gallerySwiper = new Swiper ('.gallery-bot.swiper-container', {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.flat-wrap .swiper-pagination'),
				nextButton: $('.flat-wrap .swiper-button-next'),
				prevButton:$('.flat-wrap .swiper-button-prev')
			});

			$(".gallery-thumbs.swiper-container").on('click', '.swiper-slide', function(){
				gallerySwiper.slideTo($(this).index(), 500);
			});
			
			gallerySwiper.on('onSlideChangeStart', function(){
				var index = $('.gallery-bot .swiper-slide-active').index('.gallery-bot .swiper-slide');
				previewSwiper.slideTo(index, 500);
				$('.gallery-thumbs .swiper-slide').removeClass('m-current');
				$('.gallery-thumbs .swiper-slide:eq("'+ index +'")').addClass('m-current');
			});
			
			gallerySwiper.on('onTransitionStart', function () {
				setTimeout(function(){
					sliderIndex('.flat-wrap');
				},50);
			});
			
			sliderIndex('.flat-wrap');
		};
		
		if ($('.floor-wrap .swiper-container .swiper-slide').length > 1) {
			var schemeSwiper = new Swiper ('.floor-wrap .swiper-container', {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.floor-wrap .swiper-pagination'),
				nextButton: $('.floor-wrap .swiper-button-next'),
				prevButton:$('.floor-wrap .swiper-button-prev')
			});

			schemeSwiper.on('onTransitionStart', function () {
				setTimeout(function(){
					sliderIndex('.floor-wrap');
				},50);
			});
			
			sliderIndex('.floor-wrap');
		};
		
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
				
		$('.gallery-tiles').each(function() {
			var cont = $(this);
			var galSmall = $('.gallery-small', cont);
			var galBigMain = $('.gallery-big.gallery-main', cont);
			var galBigScheme = $('.gallery-big.gallery-schemes', cont);
			var gallerySwiperMain = new Swiper ($('.swiper-container', galBigMain), {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.swiper-pagination', galBigMain),
				nextButton: $('.swiper-button-next', galBigMain),
				prevButton: $('.swiper-button-prev', galBigMain)
			});
			var gallerySwiperScheme = new Swiper ($('.swiper-container', galBigScheme), {
				slidesPerView: 1,
				paginationClickable: true,
				pagination: $('.swiper-pagination', galBigScheme),
				nextButton: $('.swiper-button-next', galBigScheme),
				prevButton: $('.swiper-button-prev', galBigScheme)
			});
			$('.open-photo', cont).on('click', function() {
				if (!$(this).data('id')) return false;
				var imgId = $(this).data('id');
				var galBig = $(this).closest('.flat-wrap').length? galBigMain : galBigScheme;
				var index = galBig.find('.img-' + imgId).closest('.swiper-slide').index();
				var swiper = $(this).closest('.flat-wrap').length? gallerySwiperMain : gallerySwiperScheme;
				swiper.slideTo(index, 0);
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
		
		//3d tour
		$('.open-tour').on('click', function() {
			var frame = $('.tour-frame', this);
			TweenMax.to($(this), 0.4, {
				onComplete: function() {
					frame.css({left: 0});
					TweenMax.to(frame, 0.4, {opacity: 1});
				}
			});
			ui.scrollTo(frame, {shift: -50});
			return false;
		});
		$('.close-tour').on('click', function() {
			var frame = $('.tour-frame');
			TweenMax.to(frame, 0.4, {
				opacity: 0,
				onComplete: function() {
					TweenMax.to(frame, 0.4, {opacity: 0});
					frame.css({left: -9999});
				}
			});
			ui.scrollTo(frame, {shift: -50});
			return false;
		});
		

				//3d video
		$('.open-video').on('click', function() {
			var frame = $('.tour-video', this);
			TweenMax.to($(this), 0.4, {
				onComplete: function() {
					frame.css({left: 0});
					TweenMax.to(frame, 0.4, {opacity: 1});
				}
			});
			ui.scrollTo(frame, {shift: -50});
			return false;
		});
		$('.close-video').on('click', function() {
			var frame = $('.tour-video');
			TweenMax.to(frame, 0.4, {
				opacity: 0,
				onComplete: function() {
					TweenMax.to(frame, 0.4, {opacity: 0});
					frame.css({left: -9999});
				}
			});
			 $('#video-frame').each(function(){
  this.contentWindow.postMessage('{"event":"command","func":"' + 'stopVideo' + '","args":""}', '*')
});
			ui.scrollTo(frame, {shift: -50});
			return false;
		});

//		$('.flat-wrap').on('click', '.gallery-bot', function(){
//			TweenMax.to(galSmall, 0.4, {
//				opacity: 0,
//				onComplete: function() {
//					galBig.css({left: 0});
//					TweenMax.to(galBig, 0.4, {opacity: 1});
//				}
//			});
//		});
		
		
	});
});