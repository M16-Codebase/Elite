define([], function() {
	var win = $(window);
	var winHeight = 0;
	var winScroll = 0;
	var oldScroll = 0;
	var scrollDown = true;
	var id = 0;
	
	var elements = {};
	var Event = function() {
		return {
			start: [0, 0],
			end: [0, 0],
			inActive: 0,
			outActive: 0,
			onActive: 0
		};
	};
	var scrollEvent = function(events) {
		for (var el in events) {
			if (typeof events[el] !== 'object') continue;
			if (!elements[el]) elements[el] = {};
			if (events[el] instanceof window.Array) {
				for (var i in events[el]) {
					if (typeof events[el][i] !== 'object') continue;
					if (typeof events[el][i]['start'] !== 'object') events[el][i]['start'] = [events[el][i]['start'] || 0, events[el][i]['start'] || 0];
					if (typeof events[el][i]['end'] !== 'object') events[el][i]['end'] = [events[el][i]['end'] || 0, events[el][i]['end'] || 0];
					elements[el][++id] = $.extend(new Event, events[el][i]);
				}
			} else {
				if (typeof events[el]['start'] !== 'object') events[el]['start'] = [events[el]['start'] || 0, events[el]['start'] || 0];
				if (typeof events[el]['end'] !== 'object') events[el]['end'] = [events[el]['end'] || 0, events[el]['end'] || 0];
				elements[el][++id] = $.extend(new Event, events[el]);
			}
		}
		scrollEvent.apply();
	};
	
	scrollEvent.remove = function(el) {
		if (typeof el === 'string') {
			delete elements[el];
		} else if (typeof el === 'object') {
			if (el instanceof window.Array) {
				for (var i in el) {
					delete elements[el[i]];
				}
			} else {
				var checkAndRemoveEvent = function(delEvent, el, event) {
					var equal = true;
					for (var opt in delEvent) {
						if (typeof delEvent[opt] === 'object') continue;
						if (delEvent[opt] !== el[event][opt]) {
							equal = false;
							break;
						}
					}
					if (equal) delete el[event];
				};
				for (var i in el) {
					if (!elements[i]) continue;
					if (typeof el[i] !== 'object') {
						if (el[i]) delete elements[i];
					} else {
						for (var event in elements[i]) {
							if (el[i] instanceof window.Array) {
								for (var j in el[i]) {
									checkAndRemoveEvent(el[i][j], elements[i], event);
								}
							} else {
								checkAndRemoveEvent(el[i], elements[i], event);
							}
						}
					}
				}
			}
		}
	};

	scrollEvent.apply = function() {
		winScroll = win.scrollTop();
		winHeight = win.height();
		scrollDown = (winScroll >= oldScroll)? true : false;
		for (var els in elements) {
			if (!$(els).length) continue;
			$(els).each(function() {
				var elData = {};
				var el = $(this);
				elData.top = el.offset().top;
				elData.height = el.outerHeight();
				elData.pos = [(winHeight + winScroll - elData.top), (elData.top + elData.height - winScroll)];
				for (var event in elements[els]) {
					var elEvent = elements[els][event];
					var dir = scrollDown? 0 : 1;
					elData.status = (elData.pos[0] < elEvent.start[dir])? 0 : ((elData.pos[1] < elEvent.end[dir])? 2 : 1);
					if (el.data('old-scroll-status-' + event) !== elData.status) {
						if (elData.status === 0) {
							if (elEvent.outActive) elEvent.outActive.call(el, elData, scrollDown);
						} else if (elData.status === 1) {
							if (elEvent.inActive) elEvent.inActive.call(el, elData, scrollDown);
						} else {
							if (elEvent.outActive) elEvent.outActive.call(el, elData, scrollDown);
						}
						el.data('old-scroll-status-' + event, elData.status);
					}
					if (elData.status === 1) {
						if (elEvent.onActive) elEvent.onActive.call(el, elData, scrollDown);
					}
				}
			});
		}
		oldScroll = winScroll;
	};
	
	win.on('scroll resize touchmove', scrollEvent.apply);
	return scrollEvent;
});