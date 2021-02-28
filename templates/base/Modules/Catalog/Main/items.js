$(function() {
	require(['ui', 'userForm', 'equalCol', 'popupFilter', 'slider'], function(ui, userForm, equalCol, popupFilter) {
		
		function paintText(elements, colors) {
			var $elements = $(elements);
			$elements.gradientText({
				colors: colors
			});
		}
		
		
		$('.goods-area').each(function() {
			equalCol($('.single-item', this));
		});
		var itemsList = $('.items-list-cont');
		
		if (window.history) {
			history.replaceState({}, '', window.location.href);
		}
				
		$(window).bind('popstate', function(event) {
			// if the event has our history data on it, load the page fragment with AJAX
			var state = event.originalEvent.state;
			if (state) {
				$.ajax({
					url: document.location + '&ajax=1',
					success: function(res){
						itemsList.fadeOut(function() {
							itemsList.html(res).fadeIn();
							setTimeout(function() {
								$('.goods-area').each(function() {
									equalCol($('.single-item', this));
								});
							}, 500);				
							ui.scrollTo(200);
							ui.initAll();
						});
					}
				});
			}
		});
		var form = $('.aside-filter');
		form.each(function() {
			var form = $(this);
			var checkFields = function() {
				$('.field', form).each(function() {
					var field = $(this);
					var clear = $('.range-reset-button', field);
					var inactive = true;
					$('INPUT, SELECT, TEXTAREA', field).each(function() {
						if ($(this).closest('.slider-wrap').length) {
							var slider = $('.slider', field);
							if ($(this).hasClass('input-min')) {
								if ($(this).val() != slider.data('min')) inactive = false;
							} else if ($(this).hasClass('input-max')) {
								if ($(this).val() != slider.data('max')) inactive = false;
							}
						} else {
							if ($(this).is(':checkbox')) {
								if ($(this).prop('checked')) inactive = false;
							} else if ($(this).val()) {
								inactive = false;
							}
						}
					});
					
					if (inactive) clear.addClass('a-hidden');
					else clear.removeClass('a-hidden');
				});
			};
			checkFields();
			
			$('.range-reset-button', form).on('click', function() {
				var field = $(this).closest('.field');
				$('INPUT:checkbox', field).prop('checked', false).change();
				$('INPUT:not(:checkbox), TEXTAREA', field).val('');
				$('.chosen', field).val('').change().trigger('liszt:updated');
				$('.slider', field).each(function() {
					var min = $(this).data('min');
					var max = $(this).data('max');
					var wrap = $(this).closest('.slider-wrap');
					min = parseFloat(min.toString().replace(',', '.'));
					max = parseFloat(max.toString().replace(',', '.'));
					$(this).slider('values', [min, max]);
					$('.input-min', wrap).val(min);
					$('.input-max', wrap).val(max);
					$('.text-min', wrap).text(min);
					$('.text-max', wrap).text(max);
				});
				checkFields();
				form.submit();
				return false;
			});
			$('INPUT, SELECT, TEXTAREA', form).on('change', function() {
				checkFields();
				form.submit();
			});
			$('.slider', form).on('slidechange', function() {
				checkFields();
				form.submit();
			});
			
			var submitTimer = 0;
			form.submit(function() {
				clearInterval(submitTimer);
				submitTimer = setInterval(function() {
					if (form.hasClass('sending')) return;
					clearInterval(submitTimer);
					userForm.submit(form, {
						dataType: null,
						data: {ajax: 1},
						afterSubmit: function(res) {
							if (!window.history) return;
							var newUrl = '';
							var first = true;
							$('INPUT, SELECT, TEXTAREA', form).each(function() {
								if ($(this).is(':disabled')) return;
								if ($(this).val() === '') return;
								if ($(this).is('.a-hidden') || $(this).closest('.a-hidden').length) return;
								if ($(this).is(':checkbox') && !$(this).prop('checked')) return;
								if (first) newUrl += '?';
								else newUrl += '&';
								first = false;
								newUrl += $(this).attr('name') + '=' + $(this).val();
							});
							if (!newUrl) newUrl = './';
							history.pushState({}, '', newUrl);
						},
						errors: function(err) {					
						},
						success: function(res) {
							checkFields();
							itemsList.fadeOut(function() {
								itemsList.html(res).fadeIn();
								setTimeout(function() {
									$('.goods-area').each(function() {
										equalCol($('.single-item', this));
									});
								}, 500);				
								ui.scrollTo(200);
								ui.initAll();
							});
						},
						serverError: function(err) {					
						}
					});
				}, 1000);
				return false;
			}).on('click', '.clear-form', function() {
				userForm.clear(form);
				return false;
			});
		});
		
		itemsList.on('click', '.sorting-panel A', function() {
			$(this).addClass('m-user-choice').siblings().removeClass('m-user-choice');
			$('.input-sort', form).attr('name', $(this).data('sort')).val($(this).data('val'));
			form.submit();
			return false;
		});
		
		itemsList.on('click', '.single-page-number A, .pg-arrows A', function(event) {
			if($(this).hasClass("m-unactive")) {
				event.preventDefault();
				return;
			}
			var page = $(this).data('page') || $(this).text();
			$('.input-page', form).val(page);
			form.submit();
			return false;
		});
		
		// variants popup
		var addToCart = function(id, count, callback) {
			if (!id) return;
			count = count || 1;
			callback = callback || function() {};
			$.post('/order/addVariantToOrder/', {
				variant_id: id,
				count: count
			}, function(res) {
				if (res.data.order_id) {
					$('.popup-add-variant-to-cart').html(res.content).dialog('open');
					paintText(".popup-add-variant-to-cart .grad-text", ["#2f3d74", "#da4b65"]);
					updateCart(res.data.order_id, callback);
				}
			}, 'json');
		};
		itemsList.on('click' , '.variants-count', function() {
			var id = $(this).data('id');
			var title = $(this).data('title');
			var newForm = $('.aside-filter').clone();
			userForm.submit(form, {
				url: '/catalog/variantsPopup/',
				data: {item_id: id},
				success: function(res) {
					$('.popup-select-variant').html(res.content);
					popupFilter($('.aside-filter'));
					$('.popup-select-variant').dialog({title: title}).dialog('open');
					$('.search-variant-result').on('click', '.add-to-cart', function() {
						addToCart($(this).data('id'));
					}).on('click', '.variant-title', function() {
						location.href = $(this).attr('href');
					});
					newForm.remove();
				}
			});
			return false;
		});	

	});
});
				



