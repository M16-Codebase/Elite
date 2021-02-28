define(['ui', 'mapstyles'], function(ui, mapstyles) {
	var initMap = function() {
		$('.contacts-map').each(function() {
			if ($(this).data('map-inited')) return;
			else $(this).data('map-inited', true);
			var cont = $(this);
			var fakeBtn = $('.btn-fake', cont);
			var mapBigCont = $('.map-big', cont);
			var mapSmallCont = $('.map-small', cont);
			var mapBig = $('.map', mapBigCont);
			var mapSmall = $('.map', mapSmallCont);
			var coords = mapSmall.data('coords');
			if (!coords) return;
			else coords = coords.split(',');
			var infoblockContent = $('.infoblock-content', mapBigCont);
			var mapStyleSimple = new google.maps.StyledMapType(mapstyles.simple);
			var mapStyleLight = new google.maps.StyledMapType(mapstyles.light);
			var gMapSmall = new google.maps.Map(mapSmall[0], {
				center: new google.maps.LatLng(coords[0]-0.00062, coords[1]),
				zoom: 13,
				scrollwheel: false,
				panControl: false,
				zoomControl: false,
				scaleControl: false,
				mapTypeControl: false,
				navigationControl: false,
				streetViewControl: false,
				overviewMapControl: false
			});
			gMapSmall.mapTypes.set('simple', mapStyleSimple);
			gMapSmall.setMapTypeId('simple');
			google.maps.event.addDomListener(window, 'resize', function() {
				var center = new google.maps.LatLng(coords[0]-0.00062, coords[1]);
				google.maps.event.trigger(gMapSmall, 'resize');
				gMapSmall.setCenter(center);
			});


      var btnCover = $('.open-map-cover').eq(0);
      var w = $(window), wk;

      w.resize(function(){
        wk = w.width() / w.height();
      }).resize();

      $('.open-map', cont).click(function() {
        $('.open-map').addClass('visible-map');
        TweenMax.to(btnCover, .8, {
          css : {
            transform: 'matrix('+ wk * 6 +', 0, 0, ' + wk * 18 + ', 0, 0)'
          },
          ease: Power1.easeInOut,
          onComplete: function() {

            mapBig.empty();
            mapBigCont.css({display: 'block', opacity: 0});
            var gMapBig = new google.maps.Map(mapBig[0], {
              center: new google.maps.LatLng(coords[0], coords[1]),
              zoom: 15,
              scrollwheel: false,
              panControl: false,
              zoomControl: true,
              scaleControl: true,
              mapTypeControl: true,
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
            gMapBig.mapTypes.set('light', mapStyleLight);
            gMapBig.setMapTypeId('light');
            google.maps.event.addDomListener(window, 'resize', function() {
              var center = new google.maps.LatLng(coords[0]-0.00062, coords[1]);
              google.maps.event.trigger(gMapBig, 'resize');
              gMapBig.setCenter(center);
            });
            TweenMax.to(mapBigCont, 0.4, {
              opacity: 1,
              onComplete: function() {
                fakeBtn.css({outlineWidth: 0});
                var marker = new google.maps.Marker({
                  position: new google.maps.LatLng(coords[0], coords[1]),
                  icon: {
                    url: '/img/marker-big.png',
                    anchor: new google.maps.Point(34, 34)
                  },
                  title: $('.item-title', infoblockContent).text(),
                  map: gMapBig
                });
                var infoBlock = new InfoBubble({
                  content: infoblockContent.html(),
                  closeBoxURL: '/img/infoblock-close.png',
                  сontainerClass: 'map-content',
                  bubbleClass: 'map-bubble',
                  disableAnimation: true,
                  anchorHeight: 12,
                  borderRadius: 0,
                  borderWidth: 0,
                  shadowStyle: 0,
                  minHeight: 80,
                  minWidth: 264,
                  arrowSize: 0
                });
                marker.addListener('click', function() {
                  infoBlock.open(gMapBig, marker);
                });
                setTimeout(function() {
                  infoBlock.open(gMapBig, marker);
                }, 300);
                $('.infra-markers .marker', mapBigCont).each(function() {
                  var mCoords = $(this).data('coords');
                  var title = $(this).data('title');
                  var img = $(this).data('img');
                  if (!mCoords) return;
                  else mCoords = mCoords.split(',');
                  new google.maps.Marker({
                    position: new google.maps.LatLng(mCoords[0], mCoords[1]),
                    title: title? title : '',
                    icon: img? img: '',
                    map: gMapBig
                  });
                });
              }
            });


          }
        });
        ui.scrollTo(cont, {shift: 3});
        return false;
      });
      $('.close-map', cont).click(function() {
        $('.open-map').removeClass('visible-map');
        TweenMax.to(btnCover, 0, {
          css : {
            transform: 'matrix(1, 0, 0, 1, 0, 0)'
          }
        });
        TweenMax.to(mapBigCont, 0.6, {
          opacity: 0,
          ease: Power3.easeInOut,
          onComplete: function() {
            mapBigCont.css({display: 'none'});
            mapBig.empty();
          }
        });
        return false;
      });


			// $('.open-map', cont).click(function() {
			// 	TweenMax.to(fakeBtn, 0.4, {
			// 		outlineWidth: cont.width()/2,
			// 		ease: Power2.easeIn,
			// 		onComplete: function() {
			// 			mapBig.empty();
			// 			mapBigCont.css({display: 'block', opacity: 0});
			// 			var gMapBig = new google.maps.Map(mapBig[0], {
			// 				center: new google.maps.LatLng(coords[0], coords[1]),
			// 				zoom: 15,
			// 				scrollwheel: false,
			// 				panControl: false,
			// 				zoomControl: true,
			// 				scaleControl: true,
			// 				mapTypeControl: true,
			// 				streetViewControl: true,
			// 				navigationControl: false,
			// 				overviewMapControl: false,
			// 				streetViewControlOptions: {
			// 					position: google.maps.ControlPosition.TOP_LEFT
			// 				},
			// 				mapTypeControlOptions: {
			// 					position: google.maps.ControlPosition.TOP_CENTER
			// 				},
			// 				zoomControlOptions: {
			// 					position: google.maps.ControlPosition.LEFT_TOP
			// 				}
			// 			});
			// 			gMapBig.mapTypes.set('light', mapStyleLight);
			// 			gMapBig.setMapTypeId('light');
			// 			google.maps.event.addDomListener(window, 'resize', function() {
			// 				var center = new google.maps.LatLng(coords[0]-0.00062, coords[1]);
			// 				google.maps.event.trigger(gMapBig, 'resize');
			// 				gMapBig.setCenter(center);
			// 			});
			// 			TweenMax.to(mapBigCont, 0.4, {
			// 				opacity: 1,
			// 				onComplete: function() {
			// 					fakeBtn.css({outlineWidth: 0});
			// 					var marker = new google.maps.Marker({
			// 						position: new google.maps.LatLng(coords[0], coords[1]),
			// 						icon: {
			// 							url: '/img/marker-big.png',
			// 							anchor: new google.maps.Point(34, 34)
			// 						},
			// 						title: $('.item-title', infoblockContent).text(),
			// 						map: gMapBig
			// 					});
			// 					var infoBlock = new InfoBubble({
			// 						content: infoblockContent.html(),
			// 						closeBoxURL: '/img/infoblock-close.png',
			// 						сontainerClass: 'map-content',
			// 						bubbleClass: 'map-bubble',
			// 						disableAnimation: true,
			// 						anchorHeight: 12,
			// 						borderRadius: 0,
			// 						borderWidth: 0,
			// 						shadowStyle: 0,
			// 						minHeight: 80,
			// 						minWidth: 264,
			// 						arrowSize: 0
			// 					});
			// 					marker.addListener('click', function() {
			// 						infoBlock.open(gMapBig, marker);
			// 					});
			// 					setTimeout(function() {
			// 						infoBlock.open(gMapBig, marker);
			// 					}, 300);
			// 					$('.infra-markers .marker', mapBigCont).each(function() {
			// 						var mCoords = $(this).data('coords');
			// 						var title = $(this).data('title');
			// 						var img = $(this).data('img');
			// 						if (!mCoords) return;
			// 						else mCoords = mCoords.split(',');
			// 						new google.maps.Marker({
			// 							position: new google.maps.LatLng(mCoords[0], mCoords[1]),
			// 							title: title? title : '',
			// 							icon: img? img: '',
			// 							map: gMapBig
			// 						});
			// 					});
			// 				}
			// 			});


			// 		}
			// 	});
			// 	ui.scrollTo(cont, {shift: -50});
			// 	return false;
			// });
			// $('.close-map', cont).click(function() {
			// 	fakeBtn.css({outlineWidth: 0});
			// 	TweenMax.to(mapBigCont, 0.4, {
			// 		opacity: 0,
			// 		onComplete: function() {
			// 			mapBigCont.css({display: 'none'});
			// 			mapBig.empty();
			// 		}
			// 	});
			// 	return false;
			// });







		});
	};
	initMap();
	return initMap;
});