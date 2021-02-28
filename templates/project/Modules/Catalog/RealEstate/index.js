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
			};
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
			if ($('.swipe-wrap.m-see-more').length && $('.swipe-wrap.m-see-more .swiper-slide').length > 2) {
				new Swiper ('.swipe-wrap.m-see-more .swiper-container', {
					spaceBetween: 32,
					slidesPerView: 2,
					slidesPerGroup: 2,
					nextButton: $('.swipe-wrap.m-see-more .swiper-button-next'),
					prevButton:$('.swipe-wrap.m-see-more .swiper-button-prev')
				});
			};
		};
		initSwipe();


		// фильтр
        // FILTER - filter.js
        var curUrl = window.location.pathname;
        var urlParams = _.compact(curUrl.split('/'));
        var curSector = urlParams[0];
        var lastParam = _.last(urlParams);

		var filter = $('.filter');
		var result = $('.filter-result');
		var filterStr = filter.formSerialize();
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
				url: window.location.pathname,
				success: function(res) {
                    var urlStr = FILTER.createUrlString(filter.formSerialize());

                    if ('history' in window) {
						// window.allowFilterFriendlyUrl = 0;
                        if ('allowFilterFriendlyUrl' in window && window.allowFilterFriendlyUrl === 1 && urlStr) {
                            history.pushState(null, null, defUrl + urlStr)
                        } else {
                            history.pushState(null, null, defUrl + '?' + filter.formSerialize())
                        }
                    }

					//if ('history' in window) {
					//	history.replaceState({}, '', '?' + filter.formSerialize());
					//}
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
					} else {
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
