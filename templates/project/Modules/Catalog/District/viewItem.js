$(function() {
	require(['ui','mapstyles'], function(ui, mapstyles) {


		if($(window).width() <= 450)
		{
			if ($('.main-sc6 .swiper-slide').length > 1) {
				var mainSwiper2 = new Swiper ('.main-sc6 .swiper .swiper-container', {
					pagination: $('.main-sc6 .swiper-pagination'),
					nextButton: $('.main-sc6 .swiper-button-next'),
					prevButton: $('.main-sc6 .swiper-button-prev'),
					slidesPerView: 1
				});
				$('.main-sc6 .swiper-button-prev, .main-sc6 .swiper-button-next').show();
			}
			else {
				$('.main-sc6 .swiper-slide').css("width","100%");
			}
		}
		else if($(window).width() > 450 && $(window).width() <= 600)
		{
			if ($('.main-sc6 .swiper-slide').length > 2) {
				var mainSwiper2 = new Swiper ('.main-sc6 .swiper .swiper-container', {
					pagination: $('.main-sc6 .swiper-pagination'),
					nextButton: $('.main-sc6 .swiper-button-next'),
					prevButton: $('.main-sc6 .swiper-button-prev'),
					slidesPerView: 2
				});
				$('.main-sc6 .swiper-button-prev, .main-sc6 .swiper-button-next').show();
			}
		}
		else if($(window).width() > 600 && $(window).width() <= 767)
		{
			if ($('.main-sc6 .swiper-slide').length > 3) {
				var mainSwiper2 = new Swiper ('.main-sc6 .swiper .swiper-container', {
					pagination: $('.main-sc6 .swiper-pagination'),
					nextButton: $('.main-sc6 .swiper-button-next'),
					prevButton: $('.main-sc6 .swiper-button-prev'),
					slidesPerView: 3
				});
				$('.main-sc6 .swiper-button-prev, .main-sc6 .swiper-button-next').show();
			}
		}
		else {
			if ($('.main-sc6 .swiper-slide').length > 4) {
				var mainSwiper2 = new Swiper ('.main-sc6 .swiper .swiper-container', {
					pagination: $('.main-sc6 .swiper-pagination'),
					nextButton: $('.main-sc6 .swiper-button-next'),
					prevButton: $('.main-sc6 .swiper-button-prev'),
					slidesPerView: 4
				});
				$('.main-sc6 .swiper-button-prev, .main-sc6 .swiper-button-next').show();
			}
		}

		/*if ($('.main-sc6 .swiper-slide').length > 4) {
		  var mainSwiper2 = new Swiper ('.main-sc6 .swiper .swiper-container', {
			pagination: $('.main-sc6 .swiper-pagination'),
			nextButton: $('.main-sc6 .swiper-button-next'),
			prevButton: $('.main-sc6 .swiper-button-prev'),
			slidesPerView: 4
		  });
		  $('.main-sc6 .swiper-button-prev, .main-sc6 .swiper-button-next').show();
		}*/


	  $('.map-cont').each(function() {
		  var cont = $(this);
		  var map = $('.map', cont);
		  var items = $('.item', cont);
		  var coords = map.data('coords');
		  var markersBounds = new google.maps.LatLngBounds();
		  var mapStyleDark = new google.maps.StyledMapType(mapstyles.dark);
		  var gMap = new google.maps.Map(map[0], {
			center: new google.maps.LatLng(coords[0], coords[1]),
			zoom: 15,
			scrollwheel: false,
			panControl: false,
			zoomControl: true,
			scaleControl: true,
			mapTypeControl: false,
			streetViewControl: true,
			navigationControl: false,
			overviewMapControl: false,
			streetViewControlOptions: {
			  position: google.maps.ControlPosition.TOP_LEFT
			},
			mapTypeControlOptions: {
			  position: google.maps.ControlPosition.TOP_CENTER
			},
			zoomControlOptions: {
			  position: google.maps.ControlPosition.LEFT_TOP
			}
		  });
		  gMap.mapTypes.set('dark', mapStyleDark);
		  gMap.setMapTypeId('dark');
		  google.maps.event.addDomListener(window, 'resize', function() {
			  var center = new google.maps.LatLng(coords[0]-0.00062, coords[1]);
			  google.maps.event.trigger(gMap, 'resize');
			  gMap.setCenter(center);
		  });
		  var openPopup = null;
		  items.each(function() {
			  var item = $(this);
			  var coords = item.data('coords');
			  if (!coords) return;
			  else coords = coords.split(',');
			  var markerPosition = new google.maps.LatLng(coords[0], coords[1]);
			  var marker = new google.maps.Marker({
				  title: $('.title', item).text(),
				  icon: {
					  url: '/img/marker-small.png',
					  anchor: new google.maps.Point(20, 20)
				  },
				  position: markerPosition,
				  map: gMap
			  });
			  markersBounds.extend(markerPosition);
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
				  if (openPopup) openPopup.close();
				  var infoBlock = new InfoBubble({
					  content: item.html(),
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
				  ui.scrollTo(cont, {shift: -50});
			  });
		  });
		  if (items.length) {
			  // центрируем и масштабируем карту по всем маркерам
			  gMap.panTo(markersBounds.getCenter(), gMap.fitBounds(markersBounds));
			  google.maps.event.addListenerOnce(gMap, 'idle', function() {
				  if (gMap.getZoom() > 15) gMap.setZoom(15);
			  });
		  }
	  });

	});
});