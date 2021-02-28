$(function() {
	require(['ui', 'popupAlert'], function(ui, popupAlert) {
		
		// addToCompare
		$('.compare').click(function() {
			if ($(this).hasClass('m-active')) {
				compare.remove($(this).data('id'));
			} else {
				compare.add($(this).data());
			}			
			return false;
		});
	
		var cont = $('.cart-page');

		// пробелы в ценах
		var priceFormat = function(num) {
			num = num.toString();
			var price = '';
			var ost = (num.length % 3) || 3;
			for (var i = 0; i < Math.ceil(num.length / 3); i++) {
				if (!i) price += num.substr(0, ost);
				else price += ' ' + num.substr((ost + 3*(i-1)), 3);
			}
			return price;
		};

		// пересчёт цен
		var recount = function() {
			var tPrice = 0;
			var tBonus = 0;
			var tWeight = $('.orders-info').hasClass('m-no-weight')? false : 0;
			var bonusRating = parseInt($('.cart-items').data('bonus')) || 0;
			$('.cart-item').each(function() {
				if ($(this).hasClass('m-deleting')) return;
				var count = parseInt($('.count-input', this).val());
				var price = parseInt($('.item-price', this).data('price'));
				var sum = count*price;
				$('.item-price.all-price .num', this).text(priceFormat(sum));
				tPrice += sum;
				var bonus = Math.floor(count*price*bonusRating/100);
				if (bonus) {
					$('.points-number .num-bonus', this).text(bonus);
					tBonus += bonus;
				}			
				if (tWeight !== false) {
					var weight = parseFloat($('.weight', this).data('weight'));
					if (!weight) {
						tWeight = false;
					} else {
						tWeight += weight*count;
					}
				}

			});
			$('.total-price .price').text(priceFormat(tPrice));
			$('.orders-info .points-number .num-bonus').text(tBonus);
			if (tWeight) {
				$('.orders-info').removeClass('m-no-weight');
				$('.total-weight .num').text(tWeight);
			} else {
				$('.orders-info').addClass('m-no-weight');
			}
		};

		// изменение кол-ва
		cont.delegate('.count-input', 'change', function() {
			var id = $(this).closest('.cart-item').data('id');
			var count = parseInt($(this).val()) || 1;
			var max = parseInt($(this).data('max')) || 999;
			if (count < 1) count = 1;
			if (count > max) count = max;
			recount();
			$.post('/order/changePositionCount/', {
				position_id: id,
				count: count
			}, function(res) {
				cont.html(res);
				updateCart($('.header-cart').data('id'));
				ui.initAll();
			});
		});

		// удаление
		cont.delegate('.delete', 'click', function() {
			var btn = $(this);
			var id = $(this).data('id');
			popupAlert.confirm({
				title: 'Удалить товар из корзины?',
				okText: 'Удалить',
				ok: function() {
					btn.closest('.cart-item').addClass('m-deleting').fadeOut(function() {
						$(this).remove();
					});
					recount();
					$.post('/order/delOrderPosition/', {position_id: id}, function(res) {
						cont.html(res.content);
						updateCart($('.header-cart').data('id'));
						ui.initAll();
						if (!$('.cart-items TR').length) {
							$('.cart-catalog-select').remove();
						}
					}, 'json');
				}
			});
			return false;
		});
		
		// очистить корзину
		$('.clear-cart').click(function() {
			popupAlert.confirm({
				title: 'Удалить все товары из корзины?',
				okText: 'Удалить',
				ok: function() {
					
				}
			});
		});
		
		// отложить заказ
		$('.delay-order').click(function() {
			$.post('/order/delay/', {}, function(res) {
				if (res.status) window.location = '/order/?delayed=1';
				else {
					popupAlert.error({
						title: 'Заказ не отложен',
						text: 'Произошла ошибка на сервеере. Обновите страницу и попробуйте еще раз'
					});
				}				
			}, 'json');
			return false;
		});
		if ($('.order-delayed-popup').length) {
			popupAlert.ok({
				title: 'Заказ отложен',
				text: 'Вы можете возобновить заказ в <a href="/profile/orders/">личном кабинете</a>'
			});
		}
		
	});
});