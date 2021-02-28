define(function() {
	var mapstyles = {};
	
	mapstyles.simple = [{
		"elementType": "labels", "stylers": [{"saturation": -100 }, {"visibility": "off"}]
	}, {
		"stylers": [{ "saturation": -100}]
	}, {
		"featureType": "poi", "stylers": [{"visibility": "off"}]
	}, {
		"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#adadad"}]
	}, {
		"featureType": "road", "stylers": [{"color": "#adadad"}]
	}, {
		"featureType": "landscape.man_made", "elementType": "geometry", "stylers": [{"visibility": "off"}]
	}, {
		"featureType": "road", "elementType": "labels", "stylers": [{"visibility": "off"}]
	}, {
		"featureType": "landscape", "stylers": [{"color": "#fafafa"}] 
	}];
	
	mapstyles.light = [{
		"featureType": "poi", "stylers": [{"visibility": "off"}] 
	}, {
		"featureType": "road", "elementType": "geometry", "stylers": [{"color": "#ffffff"}, {"saturation": -100}] 
	}, {
		"featureType": "water", "elementType": "geometry", "stylers": [{"color": "#ffffff"}] 
	}, {
		"featureType": "transit.station.bus", "stylers": [{"visibility": "off"}] 
	}, {
		"featureType": "transit.station.rail", "elementType": "geometry", "stylers": [{"saturation": -100}] 
	}, {
		"featureType": "landscape.man_made", "stylers": [{"visibility": "simplified"}, {"color": "#efefef"}] 
	}, {
		"featureType": "road", "stylers": [{"lightness": -17}] 
	}, {
		"featureType": "administrative", "elementType": "labels.text", "stylers": [{"visibility": "off"}] 
	}, {
		"featureType": "road", "elementType": "geometry", "stylers": [{"visibility": "simplified"}] 
	}, {
		"featureType": "road", "elementType": "labels", "stylers": [{"saturation": -100}, {"lightness": 42}] 
	}, {
		"featureType": "road", "elementType": "geometry", "stylers": [{"lightness": -10}] 
	}, {
		"featureType": "road", "elementType": "labels", "stylers": [{"lightness": -9}] 
	}];

	mapstyles.dark = [{ 
			"stylers": [{"invert_lightness": true}]
		},{
			"featureType": "landscape", "stylers": [{"visibility": "on"}, {"color": "#272127"}]
		},{
			"featureType": "poi", "stylers": [{"visibility": "off"}]
		},{
			"featureType": "transit.station", "stylers": [{"visibility": "off" }]
		},{
			"featureType": "water", "stylers": [{"color": "#070410"}]
		},{
			"featureType": "landscape.man_made", "elementType": "geometry", "stylers": [{"lightness": -7}]
		},{
			"featureType": "administrative", "elementType": "labels.text", "stylers": [{"visibility": "off"}]
		},{
			"featureType": "road", "elementType": "geometry", "stylers": [{"visibility": "simplified"}, {"color": "#6a666a"}, {"lightness": -48}]
		},{
			"featureType": "road", "elementType": "labels", "stylers": [{"saturation": -100}, {"lightness": -41}]
		},{
			"featureType": "road.arterial", "elementType": "labels", "stylers": [{"lightness": 39}, {"visibility": "simplified"}]
		}];

	return mapstyles;
});