(function($) {
	var gid = 0;
	var displace = 20;
	var carousels = {};
	var dataKey = 'car-id';
	var positionAttr = 'scroll-pos';
	var classNoLeft = 'm-no-left';
	var classNoRight = 'm-no-right';
	var easing = ('ui' in $)? 'swing' : 'swing';
	var mobile = ('ontouchstart' in window);
	var defaultOptions = {
		scroll: function() {},
		listSelector: 'UL',
		itemsSelector: 'LI',
		orientation: 'h',
		infinite: false,
		speed: 200
	};
 
    $.fn.carousel = function(userData, callback) { 
         if (typeof userData === 'string' || typeof userData === 'number') {
			$(this).each(function() {
				if ($(this).data(dataKey)) {
					carousels[$(this).data(dataKey)].action(userData, callback);
				}
			});
		} else {
			$(this).each(function() {
				if ($(this).data(dataKey)) {
					carousels[$(this).data(dataKey)].update(userData);
				} else {
					carousels[++gid] = new Carousel(this, gid).init(userData);
				}
			});
		}
		return this;
    };
	
	function Carousel(cont, id) {
		this.id = id;
		this.cont = $(cont);
		this.html = cont.outerHTML;
		this.touchmove = false;
		this.animated = 0;
		this.position = 0;
		this.options = {};
		this.belt = {};
		this.items = {};
		this.sizes = {};
		this.init = init;
		this.action = action;
		this.update = update;
		this.scroll = scroll;
		this.goToItem = goToItem;
		this.getCurrentPosition = getCurrentPosition;
		return this;
	}
	
	function init(options) {	
		var car = this;
		this.cont.data(dataKey, this.id);
		applyOptions.call(this, options);
		this.belt = $(this.options.listSelector, this.cont);
		this.items = $(this.options.itemsSelector, this.belt);
		if (document.readyState === 'complete') {
			setupCarousel.call(this);
		} else {
			$(window).load(function() {
				setupCarousel.call(car);
			});
		}
		if (mobile) touchEvents.call(this);
		return this;
	}
	
	function applyOptions(options) {
		options = options || {};
		for (var i in defaultOptions) {
			this.options[i] = options[i] || this.options[i] || defaultOptions[i];
		}
	}
		
	function update(newOptions) {
		applyOptions.call(this, newOptions);
	}
	
	function setupCarousel() {
		var car = this;
		var setup = function() {
			var pos = car.belt.position();
			var newSizes = [car.cont.width(), car.cont.height()];
			if (car.sizes.contHeight === newSizes[1] && car.sizes.contWidth === newSizes[0]) return;
			car.sizes.contHeight =  newSizes[1];			
			car.sizes.contWidth =  newSizes[0];
			car.sizes.beltHeight = 0;
			car.sizes.beltWidth = 0;
			car.sizes.items = [];
			car.items.each(function() {
				var itemSizes = {
					height: $(this).outerHeight(true),
					width: $(this).outerWidth(true)
				};
				car.sizes.items.push(itemSizes);				
				car.sizes.beltHeight += itemSizes.height;
				car.sizes.beltWidth += itemSizes.width;
			});
			if (car.options.orientation === 'v') {
				car.sizes.maxTop = car.sizes.beltHeight - car.sizes.contHeight - parseInt(car.items.last().css('margin-bottom'));
				if (car.sizes.maxTop < 0) car.sizes.maxTop = 0;
				if (pos.top > 0) car.belt.css({top: 0});
				else if (pos.top < -car.sizes.maxTop) car.belt.css({top: -car.sizes.maxTop});
				car.belt.css({height: car.sizes.beltHeight});
			} else {
				car.sizes.maxLeft = car.sizes.beltWidth - car.sizes.contWidth - parseInt(car.items.last().css('margin-right'));
				if (car.sizes.maxLeft < 0) car.sizes.maxLeft = 0;
				if (pos.left > 0) car.belt.css({left: 0});
				else if (pos.left < -car.sizes.maxLeft) car.belt.css({left: -car.sizes.maxLeft});
				car.belt.css({width: car.sizes.beltWidth});
			}
			car.getCurrentPosition();
		};
		car.cont.css({
			overflow: 'hidden',
			position: 'relative'
		});
		car.belt.css({position: 'relative'});
		$(window).resize(setup);
		setup();
	}
	
	function getCurrentPosition() {
		var pos = this.belt.position();
		var maxPos = 0;
		var curPos = 0;
		if (this.options.orientation === 'v') {
			var itemsTop = 0;
			curPos = pos.top;
			maxPos = this.sizes.maxTop;
			for (var i in this.sizes.items) {
				itemsTop += this.sizes.items[i].height;
				if (-pos.top < itemsTop-(0.9*this.sizes.items[i].height)) {
					break;
				} else if (-pos.top < itemsTop-5) {
					i++;
					break;
				}
			}
		} else {
			var itemsLeft = 0;
			curPos = pos.left;
			maxPos = this.sizes.maxLeft;
			for (var i in this.sizes.items) {
				itemsLeft += this.sizes.items[i].width;
				if (-pos.left < itemsLeft-(0.9*this.sizes.items[i].width)) {
					break;
				} else if (-pos.left < itemsLeft-5) {
					i++;
					break;
				}
			}
		}
		if (curPos >= 0) {
			this.cont.addClass(classNoLeft);
		} else {
			this.cont.removeClass(classNoLeft);
		}
		if (curPos <= -maxPos) {
			this.cont.addClass(classNoRight);
		} else {
			this.cont.removeClass(classNoRight);
		}
		this.position = i;
		this.cont.attr('data-' + positionAttr, i);
		this.options.scroll.call(this.cont, i);
		return i;
	}
	
	function touchEvents() {
		var car = this;
		var startX, startY, posX, posY, dX, dY;
		this.cont.bind('touchstart', function(e) {
			startX = e.originalEvent.targetTouches[0].pageX;
			startY = e.originalEvent.targetTouches[0].pageY;
			dX = dY = 0;
			car.touchmove = true;
		}).bind('touchmove', function(e) {
			if (!car.touchmove) return;
			posX = e.originalEvent.targetTouches[0].pageX || e.pageX;
			posY = e.originalEvent.targetTouches[0].pageY || e.pageY;
			dX = startX - posX;
			dY = startY - posY;
			if (Math.abs(dX) > displace || Math.abs(dY) > displace) {
				if (car.options.orientation === 'v' && dY > dX) {
					e.preventDefault();
				} else if (car.options.orientation === 'h' && dX > dY) {
					e.preventDefault();
				}				
			}
		}).bind('touchend touchcancel', function(e) {
			car.touchmove = false;
			startX = startY = dX = dY = 0;
		});
	}
	
	function action(event, callback) {
		event += '';
		callback = callback || function() {};
		if (event.match(/^\d{1,3}$/i)) {
			this.goToItem(parseInt(event));
		} else if (event.match(/^[+-]\d{1,3}$/i)) {
			this.scroll(parseInt(event));
		} else {
			switch (event) {
				case 'first':
					this.goToItem(0);
					break;
				case 'last':
					this.goToItem(this.items.length);
					break;
				case 'next':
					this.scroll('+1');
					break;
				case 'prev':
					this.scroll('-1');
					break;
				case 'options':
					this.update(callback);
					break;
				case 'destroy':
					break;
			}
		}		
	}
	
	function goToItem(index) {
		var newPos = 0;
		if (index < 0) index = 0;
		else if (index >= this.items.length) index = (this.items.length - 1);
		if (this.options.orientation === 'v') {
			for (var i = 0; i < index; i++) {
				newPos += this.sizes.items[i].height;
			}
		} else {
			for (var i = 0; i < index; i++) {
				newPos += this.sizes.items[i].width;
			}
		}
		animateScroll.call(this, -newPos);
	}
	
	function scroll(num) {		
		var index = parseInt(this.position) + parseInt(num);
		this.goToItem(index);		
	}
	
	function animateScroll(newPos) {
		var car = this;
		var hor = (this.options.orientation !== 'v');
		if (newPos > 0) newPos = 0;
		if (hor) {
			if (newPos < -this.sizes.maxLeft) newPos = -this.sizes.maxLeft;
		} else  {
			if (newPos < -this.sizes.maxTop) newPos = -this.sizes.maxTop;
		}
		var startPos = hor? this.belt.position().left : this.belt.position().top;
		var dPos = Math.abs(startPos - newPos);
		var time = Math.floor((dPos * this.options.speed) / 100);
		var options = hor? {left: newPos} : {top: newPos};
		clearInterval(this.animated);
		this.animated = setInterval(function() {
			 car.getCurrentPosition();
		}, 100);
		this.belt.stop().animate(options, time, easing, function() {
			clearInterval(car.animated);
			car.getCurrentPosition();
		});
	}
	 
}(jQuery));