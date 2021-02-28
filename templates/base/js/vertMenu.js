define(function() {	
	$('.vert-menu').each(function() {
		var cont = $(this);
		var items = $('.vm-item', cont);
		var menus = $('.vm-menu', cont);
		var triangle = false;
		var open = false;
		var coords = [];
		var menuCoords = [];
		var scat = 5;
		
		// содержит ли треугольник ABC точку P
		var containsPoint = function(a, b, c, p) {
			return ((p[0]-a[0])*(a[1]-b[1])-(p[1]-a[1])*(a[0]-b[0]) >= 0) &&
				((p[0]-b[0])*(b[1]-c[1])-(p[1]-b[1])*(b[0]-c[0]) >= 0) &&
				((p[0]-c[0])*(c[1]-a[1])-(p[1]-c[1])*(c[0]-a[0]) >= 0);
		};
		
		menus.css({display: 'none'});				
		cont.mousemove(function(e) {
			var newCoords = [e.pageX, e.pageY];
			if (triangle) {
				if (!containsPoint([coords[0], coords[1]], [menuCoords[0], menuCoords[1]], [menuCoords[2], menuCoords[3]], [newCoords[0], newCoords[1]])) {
					if (!$(e.target).closest('.vm-item').length) menus.hide();
					items.removeClass('vm-open');
					triangle = false;
					open = false;
				}
			} else {
				coords = [newCoords[0]-scat, newCoords[1]];
			}
		}).mouseleave(function() {
			items.removeClass('vm-open');
			triangle = false;
			open = false;
			menus.hide();
		});
		
		items.each(function() {
			var item = $(this);
			var menu = $('.vm-menu', item);
			if (!menu.length) return;
			var menuLoaded = false;
			var menuTop = 0;
			var menuHeight = 0;
			var menuPos = [0, 0];
			var minTop = cont.offset().top - item.offset().top;
			var maxBottom = 0;
			item.on('mouseenter mousemove', function() {
				if (open) return;
				menus.hide();
				menu.show();
				if (!menuLoaded) {
					menuLoaded = true;
					menuHeight = menu.outerHeight();
					maxBottom = minTop + cont.outerHeight() - menuHeight;
					menuTop = (item.outerHeight()>>1) - (menuHeight>>1);
					if (menuTop > maxBottom) menuTop = maxBottom;
					if (menuTop < minTop) menuTop = minTop;
					item.css({position: 'relative'});
					menu.css({
						position: 'absolute',
						top: menuTop
					});
					menuPos = [menu.offset().left, menu.offset().top];
				}
				menuCoords = [menuPos[0], (menuPos[1] - scat), menuPos[0], (menuPos[1] + menuHeight + scat)];
				item.addClass('vm-open');
				open = true;
			}).mouseleave(function() {
				triangle = true;
			});
		});
		
	});
});		