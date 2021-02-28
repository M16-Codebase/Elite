require.config({
	baseUrl: "/js/"
});

$(function() {
	require(['ui', 'actions'], function(ui, actions) {
		
		window.cart = (function() {
			return {
				add: function(id, count, callback) {
					if (!id) return;
					count = count || 1;
					callback = callback || function() {};
					$.post('/order/addVariantToOrder/', {
						variant_id: id,
						count: count
					}, function(res) {
						actions.cartAdd(res, callback);
					}, 'json');
				},
				update: function(order_id, callback) {
					if (!order_id) return;
					callback = callback || function() {};
					$.post('/order/smallCart/', {order_id: order_id}, function(res) {
						actions.cartUpdate(res, callback);
					});
				},
				remove: function() {},
				clear: function() {}
			};
		})();
		
		window.compare = (function() {
			var itemsCount = 0;
			var maxCompare = 4;
			return {
				add: function(item, clear, callback) {
					clear = clear? 1 : 0;
					callback = callback || function() {};
					if (itemsCount < maxCompare) {
						$.post('/catalog/setCompare/', {id: item.id, clear: clear}, function(res){
							if (!res.errors){
								var addItem = function() {
									actions.compareAdd(res, callback);
								};
								if (clear) {
									actions.compareClear(addItem);
								} else {
									addItem();
								}								
							} else {
								if (res.errors.main === 'full') {
									ui.popupalert.error({
										title: 'Добавление к сравнению',
										text: 'Можно сравнивать не более 4 товаров'
									});
								}
								else if (res.errors.main === 'type') {
									ui.popupalert.confirm({
										title: 'Сравнение',
										text: 'Вы добавляете в сравнение товар из категории «' + res.data['new'] + '», а в списке сравнения товары из категории «' + res.data.old + '»',
										okText: 'Добавить, очистив текущий список',
										ok: function() {
											compare.add(item, true);
										}
									});
								} else {
									ui.popupalert.error({
										title: 'Товар не добавлен',
										text: 'Ошибка при добавлении к сравнению. ' + res.errors.main
									});
								}
							}
						}, 'json');
					} else {
						ui.popupalert.error({
							title: 'Добавление к сравнению',
							text: 'Можно сравнивать не более ' + maxCompare + ' товаров'
						});
					}
				},
				remove: function(id, callback) {
					callback = callback || function() {};
					if (id !== undefined) {
						$.post('/catalog/delCompare/', {id: id}, function() {
							actions.compareRemove(id, callback);					
						});
					} else {
						$.post('/catalog/delCompare/', {}, function() {
							actions.compareClear(callback);
						});
					}
					
				}
			};
		})();		
		
		// Аутентификация через соцсети
        $('.social-auth-buttons .auth-btn').click(function(){
            $.ajax({
                url: '/welcome/getSocialAuthLink/',
                type: 'post',
                data: {
                    network_key: $(this).data('network'),
                    referrer_url: window.location.href
                },
                dataType: 'json',
                success: function(res){
                    if (res.errors === null){
                        window.location.href = res.data.auth_link;
                    }
                }
            });
            return false;
        });
		
        //проверка региона
		var checkRegion = function() {
			if (window.checkerSended === undefined) { //если мы и не хотели отправлять запрос
                clearInterval(regionCheckIntervalID);
            } else {
                if (window.checkerSended){ //проверяем, получили ли ответ от сервера
                    $('.popup-window-checkRegion').dialog({
						close: function() {
							return false;
						}
					}).dialog('open');
                    clearInterval(regionCheckIntervalID);
					ui.initAll();
                }
            }
		};
        var regionCheckIntervalID = setInterval(checkRegion, 100);

		actions.init();
	});	
    
    /*  ТЕСТ  **/
//    $.post('/order/addVariantToOrder/', {test: 1, variant_id: 4, count: 5}, function(res){
//        
//    });
});

function randomPassword() {
    var pass_length = 8;
    var allow = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var i = 1;
    var ret = '';
    while (i <= pass_length) {
        var max  = allow.length - 1;
        var num  = Math.floor(Math.random() * (max + 1));
        var temp = allow.substr(num, 1);
        ret = ret + temp;
        i++;
    }
    return ret;
}