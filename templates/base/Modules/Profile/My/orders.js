$(function() {
	require(['ui', 'popupAlert'], function(ui, popupAlert) {
		
		// восстановить заказ
		$('.undelay-order').click(function() {
			var id = $(this).data('id');
			$.post('/order/undelay/', {order_id: id}, function(res) {
				if (!res.errors) {
					popupAlert.ok({
						title: 'Возобновление заказа',
						text: 'Ваш заказ возобновлен. Мы можете приступить к <a href="/order/form/">оформлению</a>'
					});
				} else {
					popupAlert.error({
						title: 'Заказ не возобновлен',
						text: 'Возникла ошибка. Перезагрузите страницу и попробуйте еще раз'
					});
				}
			}, 'json');
			return false;
		});
		
		// variants size
		$('.variants-list').each(function() {
			var variants = $('.variant', this);			
			var changeSize = function(vars, size) {
				vars.each(function() {
					if (size === 'short') {
						$('.variant-info', this).css({display: 'none'}).fadeIn();
						$('.var-specs', this).css({display: 'block'}).slideUp();
					} else {
						$('.variant-info', this).css({display: 'inline-block'}).fadeOut();
						$('.var-specs', this).css({display: 'none'}).slideDown();
					}
					$(this).removeClass('m-short m-full').addClass('m-' + size);
				});
			};			
			$('.variants-switcher .switch-showing').click(function() {
				if ($(this).hasClass('m-current')) return false;
				var size = $(this).hasClass('m-full')? 'full' : 'short';
				$(this).addClass('m-current').siblings().removeClass('m-current');
				if (size === 'full') $('.variants-switcher .switcher').addClass('m-right');
				else $('.variants-switcher .switcher').removeClass('m-right');
				$('.variants-switcher').removeClass('m-short m-full').addClass('m-' + size);
				changeSize(variants, size);
			});
			$('.variants-switcher .switcher').click(function() {
				var right = $('.variants-switcher').hasClass('m-short')? true : false;
				$('.variants-switcher').removeClass('m-short m-full');
				if (right) {
					$('.variants-switcher .switch-showing.m-full').addClass('m-current').siblings().removeClass('m-current');
					$('.variants-switcher').addClass('m-full');
					$(this).addClass('m-right');
					changeSize(variants, 'full');
				} else {
					$('.variants-switcher .switch-showing.m-short').addClass('m-current').siblings().removeClass('m-current');
					$('.variants-switcher').addClass('m-short');
					$(this).removeClass('m-right');
					changeSize(variants, 'short');
				}
			});
			$('.change-size', variants).click(function() {
				changeSize($(this).closest('.variant'), $(this).data('size'));
			});
		});
		
		// FORM 
		$('.order-form').submit(function(){
			$('INPUT, SELECT', this).each(function() {
				if ($(this).val() === '') $(this).attr('disabled', true);
			});
		});
		
				
	});
});