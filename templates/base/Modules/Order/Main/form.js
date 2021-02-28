$(function() {	
	
	var adds = 0;
	$('.cart-form .delivery-store label').click(function(){
		adds = $(this).data('id');
		$('.cart-form .delivery-store .address-radio').removeClass('btn btn-light-grey m-current');
		$(this).addClass('btn btn-light-grey m-current');
		$('.cart-form .delivery-store .address').removeClass('m-current').addClass('a-hidden');
		$('.cart-form .delivery-store .address.adds-'+adds).addClass('m-current').removeClass('a-hidden');
	});
		
	
	// bonus slider
	$('.bonus-slide').each(function() {
		var slider = $(this);
		var id = $(this).data('id');
		var max = $(this).data('max');
		var val = $(this).data('val') || 0;
		var input = $('.bonus-input');
		var num = {};
		$(this).slider({
			range: 'min',
			min: 0,
			max: max,
			step: 1,
			value: val,
			create: function() {
				num = $('<div class="bonus-num">' + val + '</div>').appendTo($('.ui-slider-handle', slider));
			},
			slide: function(event, ui) {
				input.val(ui.value);
				num.text(ui.value);				
			},
			change: function(event, ui) {
				$.get('/order/setBonus/', {
					bonus: ui.value
				}, function(res) {
					if (res.error) {
						alert(res.error);
					} else {
						var price = res.data.order_total_price;
						$('.form-content-bottom .all-price .num').text(price);
					}
				}, 'json');
			}
		});
	});
	
	// submit
	$('.order-forming-form').each(function() {
		var form = $(this);
		form.submit(function() {
			if (form.hasClass('sending')) return false;
			var formPage = $('.tab-page:not(.a-hidden)', form);
			form.addClass('sending');
			$('.f-input', form).removeClass('m-error');
			$('INPUT, SELECT, TEXTAREA', form).each(function() {
				if ($(this).val() === '' || $(this).closest('.a-hidden').length) $(this).attr('disabled', true);
			});
			form.ajaxSubmit({
				url: '/order/checkForm/',
				type: 'POST',
				dataType: 'json',
				success: function(res) {
					form.removeClass('sending');
					$('INPUT, SELECT, TEXTAREA', form).removeAttr('disabled');
					if (res.errors) {
						for (var f in res.errors) {
							$('INPUT[name=' + f + ']', formPage).closest('.f-input').addClass('m-error');
						}
					} else if (res.url){
						 window.location = res.url;
					}
				},
				error: function(err) {
					form.removeClass('sending');
					alert('Ошибка ' + err.status);
				}
			});
			return false;
		});
	});
	
	// комментарий к заказу
	$('.open-descr-field').click(function() {
		$(this).fadeOut(150, function() {
			$(this).next('.field').slideDown();
		});		
		return false;
	});

	var mapPopup = $('.popup-map');
	var mapCont = $('.map-cont', mapPopup);	
	ymaps.ready(function() {			
		$('.address .show-map').click(function() {
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