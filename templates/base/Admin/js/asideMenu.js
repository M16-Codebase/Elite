define(function() {
	var menu = $('.aside-menu');
	if (!menu.length) return;
	$('.am-submenu-contains', menu).each(function() {
		var menuItem = $(this);
		var open = menuItem.hasClass('m-open');
		var submenu = $('.aside-submenu', menuItem);
		if (!submenu.length) return;
		submenu.css('display', (open? 'block' : 'none') );
		$('.am-item-title', menuItem).bind('click', function() {
			submenu.stop(true, true);
			if (open) {
				submenu.slideUp();
				menuItem.removeClass('m-open');
			} else {
				submenu.slideDown();
				menuItem.addClass('m-open');
			}
			open = !open;
			return false;
		});
	});
	
	var cont = $('.aside-menu-wrap');
	var button = $('.aside-menu-button');
	var closedInner = $('.aside-closed');
	var closed = cont.hasClass('m-closed');
	var openMenu = function() {
		cont.removeClass('m-closed');
		closedInner.stop().css({display: 'block'}).slideUp(function() {
			$(this).css({display: 'none'});
		});
		menu.stop().css({display: 'none'}).slideDown(function() {
			$(this).css({display: 'block'});
		});
		closed = false;
	};
	var closeMenu = function() {
		cont.addClass('m-closed');
		closedInner.stop().css({display: 'none'}).slideDown(function() {
			$(this).css({display: 'block'});
		});
		menu.stop().css({display: 'block'}).slideUp(function() {
			$(this).css({display: 'none'});
		});
		closed = true;
	};
	if (closed) {
		closedInner.css({display: 'block'});
		menu.css({display: 'none'});
	} else {
		closedInner.css({display: 'none'});
		menu.css({display: 'block'});
	}
	button.click(function() {
		if (closed) openMenu();
		else closeMenu();
	});
	closedInner.click(openMenu);
	
	
	$('.open-menu').on('click touchstart', function() {
		$('BODY').addClass('m-aside-open');
	});
	$('.close-menu, .aside-lock').on('click', function() {
		$('BODY').removeClass('m-aside-open');
	});
});