$.fancybox.defaults.touch = null;
$.fancybox.defaults.btnTpl.smallBtn = '<button data-fancybox-close class="fancybox-button fancybox-close-small fancybox-button--close" title="{{CLOSE}}"><span class="modal-close-icon"></span></button>';

var app = {};
app.showMessage = function(data){
    var title = data.title || '',
        message = data.message || '',
        box = $("#modal-message"),
        instance = $.fancybox.getInstance(),
        box_clone;

    if (box.length) {
        box.find(".modal__heading").html(title);
        box.find(".modal__text").html(message);
        
        if (instance) {
            box_clone = box.clone(false);
            box_clone.attr("id", "").append($.fancybox.defaults.btnTpl.smallBtn);
            instance.setContent(instance.current, box_clone.get(0));            
        } else {
            $.fancybox.open({
                src: box,
                type: 'inline',
                touch: null,
                buttons: [],
                toolbar: false,
                smallBtn: true
            });
        }
    }
};

$(document).ready(function(){

	// Advantages slider
	(function($){
		var elem = $(".advantages-list");
		if (!elem.length) {
			return false;
		}

		if ($(window).width() > 767) {
			return false;
		}

		var prev = elem.parent().find(".nav-arrow.prev");
		var next = elem.parent().find(".nav-arrow.next");

		elem.slick({
			infinite: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			prevArrow: prev,
			nextArrow: next,
			arrows: true,
			dots: false,
			adaptiveHeight: true
		});
	})(jQuery);

	// Services slider
	(function($){
		var elem = $(".services-list");
		if (!elem.length) {
			return false;
		}

		if ($(window).width() > 767) {
			return false;
		}

		var prev = elem.parent().find(".nav-arrow.prev");
		var next = elem.parent().find(".nav-arrow.next");

		elem.slick({
			infinite: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			prevArrow: prev,
			nextArrow: next,
			arrows: true,
			dots: false,
			adaptiveHeight: true
		});
	})(jQuery);

	// Clients slider
	(function($){
		var elem = $(".clients-slider");
		if (!elem.length) {
			return false;
		}

		var prev = elem.parent().find(".nav-arrow.prev");
		var next = elem.parent().find(".nav-arrow.next");

		elem.slick({
			infinite: true,
			slidesToShow: 2,
			slidesToScroll: 2,
			prevArrow: prev,
			nextArrow: next,
			arrows: true,
			dots: false,
			responsive: [
				{
				  breakpoint: 992,
				  settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					adaptiveHeight: true
				  }
				}
			  ]
		});
	})(jQuery);

	// Certificates
	(function($){
		var elem = $(".docs-slider");
		if (!elem.length) {
			return false;
		}

		var prev = $(".docs").find(".nav-arrow.prev");
		var next = $(".docs").find(".nav-arrow.next");
		var variableWidth = ($(window).width() < 768) ? false : true;

		elem.slick({
			infinite: true,
			slidesToShow: 2,
			slidesToScroll: 1,
			prevArrow: prev,
			nextArrow: next,
			arrows: true,
			dots: false,
			variableWidth: variableWidth,
			responsive: [
				{
				  breakpoint: 768,
				  settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					adaptiveHeight: true
				  }
				}
			  ]
		});
	})(jQuery);

	// Working with us slider
	(function($){
		var elem = $(".working-with-us-slider");
		if (!elem.length) {
			return false;
		}

		if ($(window).width() > 767) {
			return false;
		}

		var prev = elem.parent().find(".nav-arrow.prev");
		var next = elem.parent().find(".nav-arrow.next");

		elem.slick({
			infinite: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			prevArrow: prev,
			nextArrow: next,
			arrows: true,
			dots: false
		});
	})(jQuery);

	// Phone mask
	(function($){
		var elem = $(".phone-mask");
		if (!elem.length) {
			return false;
		}

		elem.mask('+7 (999) 999-99-99');
	})(jQuery);

	// Init range sliders
	(function($){
		var elem = $(".range-slider-box");
		if (!elem.length) {
			return false;
		}

		$(".range-slider-box").each(function(){
			var elem = $(this).find(".js-range-slider");
	
			var min = elem.data("min") || 0;
			var max = elem.data("max") || 0;
			var from = elem.data("from") || min;
			var to = elem.data("to") || max;

			var fromInput = elem.parent().find(".input-from");
			var toInput = elem.parent().find(".input-to");

			elem.ionRangeSlider({
				type: "double",
				min: min,
				max: max,
				from: from,
				to: to,
				hide_min_max: true,
				hide_from_to: false,
				onChange: function (data) {
					fromInput.val(data.from);
					toInput.val(data.to);
				}
			});	
		});
	})(jQuery);

	// Modals
	(function($){
		if ($(".js-modal").length) {
			$(document).on("click", ".js-modal", function (e) {

				var id = $(this).data("id") || undefined;
				if (!id) {
					console.log("modal data-id attribute is missing on trigger link.");
					return false;
				}

				$.fancybox.open({
					src: '#' + id,
					type: 'inline',
					touch: null,
					afterShow: function( instance, slide ) {
						slide.$slide.find('.phone-mask').mask('+7 (999) 999-99-99');			
					}
				});
			});
		}
	})(jQuery);

	// View Checkbox Update
	(function($){
		var elem = $("input[name='view_checkbox']");
		if (!elem.length) {
			return false;
		}

		elem.on("change", function(){
			var isChecked = $(this).prop("checked");
			var newValue = 'Нет';

			if (isChecked) {
				newValue = 'Да';
			}

			$("input[name='view']").val(newValue);
		});
	})(jQuery);

	// Nav Scroll To Section
	(function($){
		var elem = $(".nav a");
		if (!elem.length) {
			return false;
		}

		$(".nav").on("click", "a", function(e){
			e.preventDefault();

			var selector = $(this).attr("href");
			var target = $(selector);
			if (target.length) {

				if ($("body").hasClass("menu--is-active")) {
					$(".js-menu").trigger("click");
				}
				$(document).scrollTo(target, 800);
			}
		});
	})(jQuery);

	// Menu
	(function($){
		var elem = $(".js-menu");
		if (!elem.length) {
			return false;
		}

		var activeClass = 'is-active';
		var animSpeed = 300;
		var menu = $(".header__nav");
		var body = $("body");

		elem.on("click", function(e){
			e.preventDefault();

			if (elem.hasClass(activeClass)) {
				elem.removeClass(activeClass);
				menu.stop(true).slideUp(animSpeed);
				body.removeClass("menu--is-active");
			} else {
				elem.addClass(activeClass);
				menu.stop(true).slideDown(animSpeed);
				body.addClass("menu--is-active");
			}
		});
	})(jQuery);
});