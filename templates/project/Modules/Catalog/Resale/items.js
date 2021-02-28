$(function () {
	require(['ui', 'mapstyles'], function(ui, mapstyles) {


        var delayImg = function(){
            $('.delay-img').each(function(){
                $(this).css({
                    "background-image": "url(" + $(this).data('bg-img') + ")",
                });
            });
        }
		/* MAP */
		var gMap = null;
		var currentMarkers = {};
		
		var initMap = function() {
			if (!$('.map-result').length) {
				currentMarkers = {};
				gMap = null;
				return;
			} else if (gMap && $('.map-result').data('map-inited')) return;
			else $('.map-result').data('map-inited', true);
			var mapCont = $('.map-result');
			var map = $('.map', mapCont);
			var mapStyleDark = new google.maps.StyledMapType(mapstyles.dark);
			gMap = new google.maps.Map(map[0], {
				center: new google.maps.LatLng(0, 0),
				scrollwheel: false,
				panControl: false,
				zoomControl: true,
				scaleControl: true,
				mapTypeControl: false,
				navigationControl: false,
				streetViewControl: false,
				overviewMapControl: false,
				zoomControlOptions: {
					position: google.maps.ControlPosition.LEFT_TOP
				}
			});
			gMap.mapTypes.set('dark', mapStyleDark);
			gMap.setMapTypeId('dark');
		};
		var openPopup = null;
		var showItemPopup = function(id, marker) {
			initMap();
			if (!gMap) return;
			if (openPopup) openPopup.close();
			var mapItemsList = $('.map-result .items-list');
			var popup = $('.item-' + id, mapItemsList);
			var infoBlock = new InfoBubble({
				content: popup.html(),
				closeBoxURL: '/img/infoblock-close.png',
				сontainerClass: 'map-content',
				bubbleClass: 'map-bubble',
				disableAnimation: true,
				disableCenter: true,
				anchorHeight: 15,
				borderRadius: 0,
				borderWidth: 0,
				shadowStyle: 0,
				minHeight: 280,
				minWidth: 840,
				arrowSize: 0
			});
			infoBlock.open(gMap, marker);
			openPopup = infoBlock;
		};

		var updateMarkers = function() {
			initMap();
			if (!gMap) return;
			var newMarkers = {};
			var mapCont = $('.map-result');
			var items = $('.items-list .item', mapCont);
			var markersBounds = new google.maps.LatLngBounds();
			if (openPopup) openPopup.close();
			items.each(function() {
				var item = $(this);
				var id = item.data('id').toString();
				var coords = item.data('coords');
				if (!coords) return;
				else coords = coords.split(',');
				var markerPosition = new google.maps.LatLng(coords[0], coords[1]);
				markersBounds.extend(markerPosition);
				if (currentMarkers[id]) {
					newMarkers[id] = currentMarkers[id];
					return;
				}
				var marker = new google.maps.Marker({
					title: $('.title', item).text(),
					icon: {
						url: '/img/marker-small.png',
						anchor: new google.maps.Point(20, 20)
					},
					position: markerPosition,
					map: gMap
				});
				marker.addListener('click', function() {
					// центрируем карту и открываем попап
					var point1 = gMap.getProjection().fromLatLngToPoint(markerPosition);
					var point2 = new google.maps.Point(
						( 0 / Math.pow(2, gMap.getZoom()) ) || 0,
						( -100 / Math.pow(2, gMap.getZoom()) ) || 0
					);
					gMap.panTo(gMap.getProjection().fromPointToLatLng(new google.maps.Point(
						point1.x - point2.x,
						point1.y + point2.y
					)));
					showItemPopup(id, marker);
					ui.scrollTo(mapCont, {shift: -50});
				});
				currentMarkers[id] = marker;
				newMarkers[id] = marker;
			});
			// центрируем и масштабируем карту по всем маркерам
			gMap.panTo(markersBounds.getCenter(), gMap.fitBounds(markersBounds));
			google.maps.event.addListenerOnce(gMap, 'idle', function() {
				if (gMap.getZoom() > 15) gMap.setZoom(15);
			});
			// удаляем лишние маркеры
			for (var id in currentMarkers) {
				if (!newMarkers[id]) currentMarkers[id].setMap(null);
			}
			currentMarkers = newMarkers;
		};
		updateMarkers();


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

			var sliderIndex = function(swiper) {
				var cont = $(swiper).closest('.img-wrap');
				var pagin = $('.pagin', cont);
				var sPagin = $('.swiper-pagination .swiper-pagination-bullet', cont);
				var activePagin = $('.swiper-pagination .swiper-pagination-bullet-active', cont);
				pagin.text((activePagin.index()+1) + ' / ' + sPagin.length);
				//console.log('pagination', $(swiper))
			};

		// быстрый просмотр
		var filter = $('.filter');
		var result = $('.filter-result');
		var gallerySwipers = {};
		var thumbSwipers = {};
		var qvCont = $('.quickview-cont');
		var qvAnimated = false;
		var qvNoMore = false;
		var qvPage = 1;
		var initQvScripts = function(item) {
			if (item.data('qvInited')) return;
			item.data('qvInited', true);
			$('.map', item).each(function() {
				$(this).empty();
				var map = $(this);
				var coords = map.data('coords');
				if (!coords) return;
				else coords = coords.split(',');
				coords = new google.maps.LatLng(parseFloat(coords[0]), parseFloat(coords[1]));
				var mapStyleLight = new google.maps.StyledMapType(mapstyles.light);
				var qgMap = new google.maps.Map(map[0], {
					zoom: 15,
					center: coords,
					scrollwheel: false,
					panControl: false,
					zoomControl: true,
					scaleControl: true,
					mapTypeControl: false,
					navigationControl: false,
					streetViewControl: false,
					overviewMapControl: false,
					zoomControlOptions: {
						position: google.maps.ControlPosition.LEFT_TOP
					}
				});
				qgMap.mapTypes.set('light', mapStyleLight);
				qgMap.setMapTypeId('light');
				new google.maps.Marker({
					title: map.data('title'),
					icon: {
						url: '/img/marker-big.png',
						anchor: new google.maps.Point(20, 20)
					},
					position: coords,
					map: qgMap
				});
			});
			
			//swiper
			$('.gallery-bot.swiper-container', item).each(function(index){
				var itemIndex = item.index('.quick-item');
				var tIndex = String(index) + String(itemIndex);
				$(this).addClass('s'+tIndex);
				$(this).closest('.img-wrap').find('.swiper-pagination').addClass('p'+tIndex);
				$(this).closest('.img-wrap').find('.swiper-button-next').addClass('r'+tIndex);
				$(this).closest('.img-wrap').find('.swiper-button-prev').addClass('l'+tIndex);

				var gallerySwiper = new Swiper('.s'+tIndex, { 
					slidesPerView: 1,
					paginationClickable: true,
					pagination: '.p'+tIndex,
					nextButton: '.r'+tIndex,
					prevButton: '.l'+tIndex,
				});

				gallerySwipers[tIndex] = gallerySwiper;

				gallerySwiper.on('onSlideChangeStart', function(){
					var index = $('.s'+tIndex + ' .swiper-slide-active').index('.s'+tIndex + ' .swiper-slide');
					thumbSwipers[tIndex + 't'].slideTo(index, 500);
					$('.s'+tIndex + 't' + '.gallery-thumbs .swiper-slide').removeClass('m-current');
					$('.s'+tIndex + 't' + '.gallery-thumbs .swiper-slide:eq("'+ index +'")').addClass('m-current');
				});
//				смена пагинации
				gallerySwiper.on('onTransitionStart', function () {
					setTimeout(function(){
						sliderIndex('.s'+tIndex);
					},50);
				});

				sliderIndex('.s'+tIndex);
			});

			$('.gallery-thumbs.swiper-container', item).each(function(index){
				var itemIndex = item.index('.quick-item');
				var tIndex = String(index) + String(itemIndex) + 't';

				$(this).addClass('s'+tIndex);

				var thumbSwiper = new Swiper('.s'+tIndex, { 
					slidesPerView: 7,
					slideToClickedSlide: true,
					spaceBetween: 10
				});
				thumbSwipers[tIndex] = thumbSwiper;
				$('.s'+tIndex).on('click', '.swiper-slide', function(){
					gallerySwipers[String(index) + String(itemIndex)].slideTo($(this).index(), 500);
					return false;
				});
			});
		};
		var getQv = function(page) {
			var req = {quickView: 1};
			if (page) req.page = page;
			ui.form.submit(filter, {
				data: req,
				method: 'GET',
				success: function(res) {
					if (!page) {
						qvCont.html(res.content);
					} else {
						qvCont.append(res.content);
					}
					if (!res.content) qvNoMore = true;
					ui.initAll();
				}
			});
		};
		var closeQv = function() {
			$('BODY').removeClass('ovh');
			TweenMax.to(qvCont, 0.4, {
				opacity: 0,
				onComplete: function() {
					qvCont.hide();
				}
			});
		};
		result.on('click', '.cover-controls .quickview', function() {
			var id = $(this).data('id');
			var item = $('.item-' + id, qvCont);
			if (!item.length) return false;
			item.show().css({opacity: 1}).addClass('m-current').siblings().hide().removeClass('m-current');
			qvCont.css({opacity: 0, display: 'block'});
			$('BODY').addClass('ovh');
			if (item.prev().length) $('.flat-arrow.m-prev', qvCont).show();
			else $('.flat-arrow.m-prev', qvCont).hide();
			if (item.next().length) $('.flat-arrow.m-next', qvCont).show();
			else $('.flat-arrow.m-next', qvCont).hide();
			TweenMax.to(qvCont, 0.4, {opacity: 1});
			initQvScripts(item);
			return false;
		});
		qvCont.on('click', '.close-quickview', function() {
			closeQv();
			return false;
		});
		qvCont.on('click', function(e) {
			if (!$(e.target).closest('.quick-item').length) {
				closeQv();
				return false;
			}
		});
		qvCont.on('click', '.flat-arrow.m-prev', function() {
			if (qvAnimated) return false;
			qvAnimated = true;
			var item = $('.quick-item.m-current', qvCont);
			var prev = item.prev();
			if (!prev.length) return false;
			TweenMax.to(item, 0.4, {
				opacity: 0,
				onComplete: function() {
					qvCont.scrollTop(0);
					item.hide().removeClass('m-current');
					prev.addClass('m-current').css({display: 'block', opacity: 0});
					if (prev.prev().length) $('.flat-arrow.m-prev', qvCont).show();
					else $('.flat-arrow.m-prev', qvCont).hide();
					$('.flat-arrow.m-next', qvCont).show();
					initQvScripts(prev);
					TweenMax.to(prev, 0.4, {opacity: 1});
					qvAnimated = false;
				}
			});
			return false;
		});
		qvCont.on('click', '.flat-arrow.m-next', function() {
			if (qvAnimated) return false;
			qvAnimated = true;
			var item = $('.quick-item.m-current', qvCont);
			var next = item.next();
			if (!next.length) return false;
			TweenMax.to(item, 0.4, {
				opacity: 0,
				onComplete: function() {
					qvCont.scrollTop(0);
					item.hide().removeClass('m-current');
					next.addClass('m-current').css({display: 'block', opacity: 0});
					if (!next.next().next().length && !qvNoMore) {
						getQv(++qvPage);
					}
					if (next.next().length) $('.flat-arrow.m-next', qvCont).show();
					else $('.flat-arrow.m-next', qvCont).hide();
					$('.flat-arrow.m-prev', qvCont).show();
					initQvScripts(next);
					TweenMax.to(next, 0.4, {opacity: 1});
					qvAnimated = false;
				}
			});
			return false;
		});
		getQv();

		// фильтр
		// FILTER - filter.js
		var filterStr = filter.formSerialize();
        var curUrl = window.location.pathname,
            urlParams = _.compact(curUrl.split('/')),
            curSector = urlParams[0],
            lastParam = _.last(urlParams);

		var submitFilter = function(opt) {
            if (!opt && filter.formSerialize() === filterStr) return;
			filterStr = filter.formSerialize();
			var mapView = !!$('.map-result', result).length;
			var data = {ajax: 1};
            var curUrl = window.location.pathname;
            var defUrl = FILTER.cleanUri(curUrl);
			if (opt) data = _.extend(data, opt);
			ui.form.submit(filter, {
				data: data,
				ignoreempty: 1,
                url: defUrl,
				//url: curUrl,
				success: function(res) { 
					var urlStr = FILTER.createUrlString(filter.formSerialize());

					if ('history' in window) {
                        if ('allowFilterFriendlyUrl' in window && window.allowFilterFriendlyUrl === 1 && urlStr) {
                            history.pushState(null, null, defUrl + urlStr)
                        } else {
                            history.pushState(null, null, defUrl + '?' + filter.formSerialize())
                        }
                    }

					var changeInner = false;
					var content = $('<DIV />').html(res.content);
					var fullContent = content;
					var mapContent = !!$('.map-result', content).length;
					if ((mapView && mapContent) || (!mapView && !mapContent)) {
						content = $('.items-list', content);
						changeInner = true;
					}
					var toChange = changeInner? $('.items-list', result) : result;
					if (opt && opt.page) {
						var newItems = $('.item-wrap', content).css({opacity: 0});
						var seeMore = $('.more-row', fullContent);
						toChange.append(newItems);
						newItems.each(function(i) {
							var item = $(this);
							setTimeout(function() {
								TweenMax.to(item, 0.4, {opacity: 1});
							}, i*200);
						});
						$('.more-row', result).replaceWith(seeMore);
                                                initSwipe();
                                                delayImg();
                                                updateMarkers();
						setTimeout(function() {
							getQv(opt.page);
						}, 100);
					} else {
						setTimeout(function() {
							getQv();
						}, 100);
						TweenMax.to(toChange, 0.4, {
							opacity: 0,
							onComplete: function() {
								if (changeInner) {
									toChange.replaceWith(content.css({opacity: 0}));
									toChange = content;
								} else {
									toChange.html(res.content);
								}
								TweenMax.to(toChange, 0.4, {opacity: 1});
								updateMarkers();
								initSwipe();
                                delayImg();
							}
						});
					}
				}
			});
		};

		filter.on('change', 'INPUT[type="checkbox"], INPUT[type="radio"]', function() {
			var checkBox = $(this);
			var label = checkBox.closest('LABEL');
			if (checkBox.is(':checked')) {
				if (checkBox.data('radio') !== '') {
					$(checkBox.data('radio') + '-radio').not(checkBox).prop('checked', false).closest('LABEL').removeClass('m-current');
				}
				label.addClass('m-current');
			} else {
				label.removeClass('m-current');
			}
		});
		if (!$('.order-radio:checked', filter).length) {
			$('.order-radio[data-default]', filter).prop('checked', true).change();
		}
		if (!$('.view-radio:checked', filter).length) {
			$('.view-radio[data-default]', filter).prop('checked', true).change();
		}
		$('INPUT[type="checkbox"], INPUT[type="radio"]', filter).change();
		filter.on('submit', function() {
			submitFilter();
			return false;
		});
		filter.on('change', 'INPUT:not(.range-input), SELECT, TEXTAREA', function() {
			submitFilter();
		});
		filter.on('slidechange', '.slider', function() {
			submitFilter();
		});
		result.on('click', '.see-more', function() {
			var page = $(this).data('page');
			if (!page) return false;
			submitFilter({page: page});
			return false;
		});
		
	});
});