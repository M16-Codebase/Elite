define(function() {
	var win = $(window);
	var defaultOpt = {
		cont: {},
		underTop: function() {},
		overTop: function() {},
		overMax: function() {},
		max: Number.POSITIVE_INFINITY,
		top: 0,
		leftShift: 0,
		topShift: 0,
		maxShift: 0
	};
	var float = function(userEl, userOpt) {
		if (!userEl) return;
		else if (!userEl.jquery) var el = $(userEl);
		else var el = userEl;
		if (!el.length) return;
		var opt = {};
		userOpt = userOpt || {};
		for (var i in defaultOpt) {
			opt[i] = userOpt[i] || defaultOpt[i];
		}
		win.on('scroll resize load touchmove', function() {
			if (!userEl.jquery) {
				el = $(userEl);
				if (!el.length) return;
			}
			var scroll = win.scrollTop();
			var top = opt.top;
			var max = opt.max;
			var left = 0;
			var vis = opt.cont.is(':visible');
			if (opt.topShift) {
				top = top + opt.topShift;
			}
			if (opt.cont.length) {
				top = vis? top + opt.cont.offset().top : 0;
				max = vis? top + opt.cont.outerHeight() - el.outerHeight(true) : 0;
				left = opt.cont.offset().left - win.scrollLeft() + opt.leftShift;
			}
			if (opt.maxShift) {
				max = max + opt.maxShift;
			}
			if (max <= top) max = top = Number.POSITIVE_INFINITY;
			if (scroll > top && scroll < max) {
				el.css({left: left});
				opt.overTop.call(el);
			} else if (scroll >= max) {
				el.css({left: 0});
				opt.overMax.call(el);
			} else {
				el.css({left: 0});
				opt.underTop.call(el);
			}
		});
	};
	
	return float;
});