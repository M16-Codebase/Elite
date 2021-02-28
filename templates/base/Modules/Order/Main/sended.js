$(function() {
	
	var mapPopup = $('.popup-map');
	var mapCont = $('.map-cont', mapPopup);	
	ymaps.ready(function() {			
		$('.delivery-adds-info .show-map').click(function() {
			mapCont.empty();
			var coords = $(this).data('coords').split(',');
			var title = $(this).data('title') || 'Карта расположения магазина';
			console.log(coords);
			var map = new ymaps.Map(mapCont[0], {
				behaviors: ['default', 'scrollZoom'],
				center: [coords[1], coords[0]],
				zoom: 15
			});
			var myPlacemark = new ymaps.Placemark([coords[1], coords[0]], {}, {
				//iconImageHref: '/templates/img/icons/placemark.png',
				//iconImageSize: [77, 58],
				//iconImageOffset: [-36, -54]
			});
			map.geoObjects.add(myPlacemark);
			mapPopup.dialog({title: title}).dialog('open');
			return false;
		});
	});

});