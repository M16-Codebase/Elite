define(['ui', 'editContent', 'message', 'editItemProps'], function(ui, editContent, message, editItemProps) {
	
	var initVariants = function() {
		$('.variants-page').each(function() {
			var cont = $(this);
			if (cont.data('variants-init')) return;
			else cont.data('variants-init', true);
			var changeVarSelect = $('.choose-variant-to-edit SELECT', cont);
			
			if ($('.edit_properties_form', cont).length) {
				var startVarId = $('.edit_properties_form', cont).data('variant-id');
				changeVarSelect.val(startVarId);
			}
			
			// выбор варианта для редактирования
			var originLink = $('.switch-type .type-view').attr('href');
			var originEditLink = cont.data('origin');
			cont.on('change', '.choose-variant-to-edit SELECT', function() {
				var variantId = parseInt($(this).val());
				var itemLink = $(this).data('item-url');
				if (!variantId) return;
				reloadVariants(variantId, 'add', function() {
					if (window.history) {
						history.replaceState({}, '', originEditLink + '&v=' +  variantId);
					}	
					$('.switch-type .type-view').attr('href', originLink + '&v=' +  variantId);
					$('.page-header .admin_link').attr('href', itemLink + 'v' +  variantId + '/');
					cont.data('variant-url', itemLink + 'v' +  variantId + '/');
				});
			});
			
			// добавить вариант
			cont.on('click', '.variant-actions .action-add', function() {
				reloadVariants(0, 'create');
				return false;
			});
			
			// копирование варианта
			cont.on('click', '.action-copy', function() {
				reloadVariants($('.edit_properties_form', cont).data('variant-id'), 'copy');
				return false;
			});

		

			// удаление
			cont.on('click', '.aside-panel .action-delete', function() {
				var variantId = $('.edit_properties_form', cont).data('variant-id');
				message.confirm({
					text: 'Подтвердите удаление варианта.',
					type: 'delete',
					ok: function() {
						$.post('/catalog-item/deleteVariant/', {
							id: $('.edit-item-tabs').data('item_id'),
							variant_id: variantId
						}, function(){
							reloadVariants(null, 'del');
						}).error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});

			// открыть окно для изменения сортировки
			cont.on('click', '.variant-actions .action-sort', function() {
				var itemId = cont.closest('.content-scroll-cont').data('item-id');
				editContent.open({
					getform: '/catalog-item/listVariants/',
					getformdata: {id: itemId},
					getformmethod: 'get',
					getformtype: 'json',
					loadform: function() {
						$(this).closest('.edit-content').on('ui-sortable-sorted', function() {
							$(window).resize();
							ui.initAll();
						});
					},
					success: function(res) {
						message.ok('Порядок сохранен.');
					}
				});
				return false;
			});
			
			function reloadVariants(variantId, type, callback) {
				callback = callback || function() {};
				var itemId = cont.closest('.content-scroll-cont').data('item-id');
				if (type === 'copy' || type === 'create') {
					$.post('/catalog-item/saveVariant/', {
						id: itemId,
						copy_variant: variantId
					}, function(res) {
						if (res.content) {
							$('.variants-block', cont).html(res.content);
							editItemProps();
							$(window).resize();
							ui.initAll();
							callback(res);
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				} else {
					$.post('/catalog-item/itemVariants/', {
						id: itemId,
						v: variantId
					}, function(res){
						cont.html(res.content);
						cont.closest('.tabs-cont').find('.edit-item-tabs .var-count').text($('.choose-variant-to-edit SELECT OPTION', cont).length - 1);
						editItemProps();
						$(window).resize();
						ui.initAll();
						callback(res);
					},'json').error(function(err) {
						message.serverErrors(err);
					});
				}
			};
			reloadVariants(cont.data('variant'));
		});
	};
	
	initVariants();
	$(window).on('initVariants', initVariants);
	return initVariants;
});
