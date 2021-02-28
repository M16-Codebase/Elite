jQuery(document).ready(function($){

	var optin_triggered = false,
		optin_percent = 0,
		optin_time = 0;
	
	var essb_optin_flyout_show = function(event) {
		
		if (optin_triggered) return;
		
		var base_element = '.essb-optinflyout-'+event;
		var base_overlay_element = '.essb-optinflyout-overlay-'+event;
		
		if (!$(base_element).length) return;
		
		var singleDisplay = $(base_element).attr('data-single') || '';
		if (singleDisplay == '1') {
			var cookie_name = "essbOptinFlyout";
			var cookieSet = essbGetCookie(cookie_name);
			if (cookieSet == "yes") { return; }
			essbSetCookie(cookie_name, "yes", 14);
		}
		
		jQuery.fn.extend({
	        center: function () {
	            return this.each(function() {
	                var top = (jQuery(window).height() - jQuery(this).outerHeight()) / 2;
	                var left = (jQuery(window).width() - jQuery(this).outerWidth()) / 2;
	                jQuery(this).css({position:'fixed', margin:0, top: (top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
	            });
	        }
	    }); 
		
		var win_width = jQuery( window ).width();
		var doc_height = jQuery('document').height();
		
		var base_width = 500;
		if (win_width < base_width) { base_width = win_width - 60; }
		
		$(base_element).css( { width: base_width+'px'});
		
		$(base_element).fadeIn(400);
		//$(base_overlay_element).fadeIn(200);
		
		$(base_element).addClass('active-flyout');
		//$(base_overlay_element).addClass('active-flyout-overlay');
		
		optin_triggered = true;
	}
	
	var essb_optinflyout_close = function() {
		

		$(".active-flyout").fadeOut(200);
		$('.active-flyout').removeClass('active-flyout');
		
		//$(".active-flyout-overlay").fadeOut(400);
		//$('.active-flyout-overlay').removeClass('active-flyout-overlay');
	}

	var essb_booster_exit = function() {
		var e = window.event;
		
		var from = e.relatedTarget || e.toElement;

		// Reliable, works on mouse exiting window and user switching active program
		if(!from || from.nodeName === "HTML") {
			essb_optin_flyout_show('exit');
		}
	}
	
	if ($('.essb-optinflyout-exit'))
		$(document).mouseout(essb_booster_exit);
	
	var essb_flyout_scroll = function() {
		if (optin_triggered) { return; }
		
		var current_pos = jQuery(window).scrollTop();
		var height = jQuery(document).height()-jQuery(window).height();
		var percentage = current_pos/height*100;	
		
		if (percentage > optin_percent && optin_percent > 0) {
			essb_optin_flyout_show('scroll');
		}
	}
	
	if ($('.essb-optinflyout-scroll')) {
		optin_percent = $('.essb-optinflyout-scroll').attr("data-scroll") || "";
		optin_percent = parseFloat(optin_percent);
		$(window).scroll(essb_flyout_scroll);
	}
	
	if ($('.essb-optinflyout-time')) {
		optin_time = $('.essb-optinflyout-time').attr("data-delay") || "";
		optin_time = parseFloat(optin_time);
		optin_time = optin_time * 1000;
		setTimeout(function(){ essb_optin_flyout_show('time'); }, optin_time);
	}
	
	
	$('.essb-optinflyout-overlay').each(function() {
		
		$(this).click(function(e) {
			e.preventDefault();
			
			essb_optinflyout_close();
		});
	});
	
	$('.essb-optinflyout-close').each(function() {
		
		$(this).click(function(e) {
			e.preventDefault();
			
			essb_optinflyout_close();
		});
	});
  
	function essbSetCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toGMTString();
	    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
	}

	function essbGetCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i].trim();
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	}
});