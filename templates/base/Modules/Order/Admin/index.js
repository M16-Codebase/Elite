$(function(){
	require(['ui', 'editContent', 'message', 'editItemProps'], function(ui, editContent, message, editItemProps) {
		var ordersList = $('.orders-list');
		
		ordersList.on('ui-slidebox-beforeOpen', '.slidebox', function() {
			setTimeout(function() {
				$(window).resize();
			}, 100);
		}).on('ui-slidebox-open ui-slidebox-close', '.slidebox', function() {
			$(window).resize();
		});
		
		// фильтр
		ui.form($('.items-filter'), {
			method: 'get',
			success: function(res) {
				history.replaceState({}, '', '?' + $(this).formSerialize());
				if (res.content) {
					ordersList.html(res.content);
				}
				$(window).resize();
				ui.initAll();
			},
			errors: function(errors) {
				message.errors({errors: errors});
			},
			serverError: function(err) {
				message.serverErrors(err);
			}
		});
        
		// Добавить заказ
		$('.content-top .action-button.action-add').click(function(){
            var type_id = $(this).data('type_id');
			editContent.open({
				getform: '/order-admin/create/',
                getformmethod: 'post',
                getformdata: {
					type_id: type_id
				},
				method: 'post',
				getformtype: 'json',
				customform: true,
				loadform: function() { 
					$('FORM', this).prepend('<input type="hidden" name="type_id" value="'+type_id+'" />');
					editItemProps();
				}
			});
			return false;
		});

		// Редактировать заказ
		ordersList.on('click', '.action-edit', function() {
			var orderId = $(this).closest('.order').data('id');
			editContent.open({
				getform: '/order-admin/edit/',
				getformdata: {
					id: orderId
				},
				method: 'post',
				getformtype: 'json',
				class: 'edit_properties_form',
				loadform: function() {
					$(this).addClass('item-props-edit');
					editItemProps();
				},
				customform: true
			});
			return false;
		});

		// Добавить позицию
		ordersList.on('click', '.order-aside .action-add', function() {
			var orderId = $(this).closest('.order').data('id');
			editContent.open({
				form: '.add-position-form',
				clearform: 1,
				formdata: {
					'order_id': orderId
				},
				loadform: function() {
					$('.add-position-form .content-top H1').text('Добавление позиции к заказу №' + orderId);
					$('.add-position-form .find-variant').empty().hide();
				},
				url: '/order-admin/findVariant/',
				success: function(res) {
					$('.add-position-form .find-variant').hide().html(res.content).fadeIn();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		$('.add-position-form').on('click', '.action-search', function() {
			$(this).closest('.add-position-form').submit();
			return false;
		});
		$('.add-position-form').on('click', '.action-add', function() {
			var form = $(this).closest('.add-position-form');
			var orderId = $('INPUT[name="order_id"]', form).val();
			var varId = $('INPUT[name="variant_id"]', form).val();
			$.post('/order-admin/addVariant/?opened=1', {
				variant_id: varId,
				order_id: orderId
			}, function(res) {
				if (res.errors) {
					if (res.errors.add) {
						message.errors({
							errors: res.errors.add,
							errorsText: {
								'count:too_small': 'Не указано количество.',
								'available:incorrect': 'Нет в наличии.',
								'price:empty': 'Не указана цена.'
							}
						});
					}
					else message.errors(res);
				} else if (res.data.added_position) {
					$('.orders-list .order-' + orderId).empty().html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				} else {
					message.errors({
						text: 'В заказе уже есть такая позиция.'
					});
				}
			}, 'json').error(function(err) {
				message.serverErrors(err);
			});
			return false;
		});
		
		// Удалить позицию
		ordersList.on('click', '.order-inner .action-delete', function() {
			if ($(this).hasClass('m-inactive')) return false;
			var btn = $(this);
			message.confirm({
				text: 'Подтвердите удаление позиции из заказа.',
				target: btn.closest('.order-position'),
				type: 'delete',
				ok: function() {
					btn.addClass('m-inactive');
					var posId = btn.closest('.order-position').data('position_id');
					var orderId = btn.closest('.order').data('id');
					$.post('/order-admin/delOrderPosition/?opened=1', {
						position_id: posId, 
						order_id: orderId
					}, function(res){
						if (res.errors){
							message.errors(res);
						} else {
							$('.orders-list .order-' + orderId).empty().html(res.content);
							$(window).resize();
							ui.initAll();
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				}
			});
			return false;
		});
		
		// Изменение количества и цены
		var countPriceSending = false;
		ordersList.on('focus', '.order-inner .count-price INPUT', function() {
			var start = $(this).val();
			$(this).data('start', start);
			$(this).val($(this).data('value'));
		}).on('blur', '.order-inner .count-price INPUT', function() {
			if ($(this).val() == $(this).data('value')) {
				$(this).val($(this).data('start'));
			}
		}).on('change', '.order-inner .count-price INPUT', function() {
			$(this).blur();
			if (countPriceSending) {
				$(this).focus();
				return false;
			}
			countPriceSending = true;
			var input = $(this);
			var order = input.closest('.order-cont');
			var isPrice = input.hasClass('price-input');
			var posId = input.closest('.order-position').data('position_id');
			var url = isPrice? '/order-admin/changePrice/?opened=1' : '/order-admin/changeCount/?opened=1';
			var data = isPrice? {
				position_id: posId,
				price: input.val()
			} : {
				position_id: posId,
				count: input.val()
			};
			$.post(url, data, function(res) {
				countPriceSending = false;
				if (res.errors) {
					input.data('value', input.val());
					message.errors(res);
				} else {
					order.empty().html(res.content);
					$(window).resize();
					ui.initAll();
				}
			}, 'json').error(function(err) {
				input.data('value', input.val());
				countPriceSending = false;
				message.serverErrors(err);
			});
		}).on('keyup', '.order-inner .count-price INPUT', function(e) {
			if (e.keyCode === 13) {
				$(this).change();
				return false;
			}
		});
		
	});
});