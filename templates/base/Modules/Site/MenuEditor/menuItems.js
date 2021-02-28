$(function() {
    require(['ui', 'userForm','editContent', 'message'], function(ui, userForm, editContent, message) {
		
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.create-menu-item-form',
				clearform: true,
				formdata: {
					parent_id: '',
				},
				success: function(res) {
					if (res.errors === null){
						$('.white-body').html(res.content);
						$(window).resize();
						editContent.close();
					} else {
						message.errors(res);
					}
				}
			});
		});
//		ui.sortable('.sub-sortable', ({cont: '.sortable'}));
		//добавить подраздел
		$('.white-body').on('click','.action-add', function() {
			var itemData = $(this).closest('.wblock');
			editContent.open({
				form: '.create-menu-item-form',
				clearform:true,
				formdata: {
					parent_id: itemData.data('id')
				},
				success: function(res) {
					if (res.errors === null){
						$('.white-body').html(res.content);
						$(window).resize();
						editContent.close();
					} else {
						message.errors(res);
					}
				}
			});
		});
		$('.white-body').on('click','.action-edit', function() {
			var itemData = $(this).closest('.white-block-row');
			editContent.open({
				getform: '/menu-editor/menuItemFields/',
				getformtype: 'json',
				getformdata: {
					id: itemData.data('id')
				},
				success: function(res) {
					if (res.errors === null){
						$('.white-body').html(res.content);
						editContent.close();
					} else {
						message.errors(res);
					}
				}
			});
		});
		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			if (confirm('Удалить меню')){
				$.ajax({
					url: '/menu-editor/deleteMenuItem/',
					type: 'post',
					data: {id: id},
					dataType: 'json',
					success: function(res){
						if (res.errors === null){
							$('.white-body').html(res.content);
							$(window).resize();
						} else {
							message.errors(res);
						}
					}
				});
			}
			return false;
		});
        var menuItemsList = $('.menu-items-list');
//        var initQ = function() {
//            $('.menu-item', menuItemsList).each(function() {
//                if ($(this).data('q-inited')) return;
//                $(this).data('q-inited', true);
//                var cont = $(this);
//                var answer = $('.new-answer', cont);
//                var newId = parseInt($('.answer-item:last', cont).data('id')) + 1;
//                answer.attr('data-id', newId);
//                $('.add-a', answer).attr('name', $('.add-a', answer).data('name').replace('[]', '[' + newId + ']'));
//            });
//        };
//        initQ();
        var initSort = function(obj) {
            obj.sortable({
                handle: '.drag-drop',
                items: 'li.menu-item',
                stop: function(event, sortUi) {
                    var menuId = parseInt(sortUi.item.data('id'));
                    var oldPosition = parseInt(sortUi.item.data('position'));
                    var newPosition = parseInt(sortUi.item.next().data('position'));
                    if (oldPosition !== newPosition - 1) {
                        if (isNaN(newPosition) || newPosition > oldPosition) {
                            newPosition = parseInt(sortUi.item.prev().data('position'));
                        }
                        $.ajax({
                            url: "/menu-editor/changePosition/",
                            type: "POST",
                            dataType: 'json',
                            data: ({
                                id: menuId,
                                position: newPosition
                            }),
                            success: function(res) {
                                menuItemsList.html(res.content);
                                ui.initAll();
                                initAllSortableLists();
//                                initQ();
                            }
                        });
                    }
                }
            });
        };
		$('.menu-sortable').on('ui-sortable-sorted',function(){
		   ui.sortable('.sub-menu');
		});
        var initAllSortableLists = function(){
            initSort(menuItemsList);
            $.each($('ul.sortable', menuItemsList), function(){
                var list = $(this);
            });
        };
        initAllSortableLists();

        menuItemsList.on('click', '.menu-item .edit-item', function(evt){
            evt.stopPropagation();
            $.ajax({
                url: '/menu-editor/menuItemFields/',
                type: 'post',
                data: {id: $(this).data('id')},
                dataType: 'json',
                success: function(res){
                    if (res.errors === null){
                        $('.popup-window-editMenuItem .ribbed').html(res.content);
                        $('.popup-window-editMenuItem').dialog({title: 'Редактирование пункта меню'}).dialog('open');
                    }
                }
            });
            return false;
        }).on('click', '.menu-item .delete-item', function(){
            if (confirm('Вы действительно хотите удалить элемент меню')){
                $.ajax({
                    url: '/menu-editor/deleteMenuItem/',
                    type: 'post',
                    data: {id: $(this).data('id')},
                    dataType: 'json',
                    success: function(res){
                        if (res.errors === null){
                            $('.menu-items-list').html(res.content);
                        } else {
                           message.errors(res);
                        }
                    }
                })
            }
            return false;
        }).on('click', '.menu-item .add-item', function(){
            addItem($(this).data('menu_id'), $(this).data('parent_id'));
            return false;
        });

        var addItem = function(menu_id, parent_id){
            var popup = $('.popup-window-editMenuItem');
            $('input', popup).val('');
            $('input[name="menu_id"]', popup).val(menu_id);
            $('input[name="parent_id"]', popup).val(parent_id);
            popup.dialog({title : 'Новый пункт меню'}).dialog('open');
        }

        $('.action-add A').click(function(){
            addItem($(this).data('menu_id'), null);
            return false;
        });

        $('.popup-window-editMenuItem form').submit(function(evt){
            evt.preventDefault();
            $(this).ajaxSubmit({
                type: 'post',
                dataType: 'json',
                success: function(res){
                    if (res.errors === null){
                        $('.menu-items-list').html(res.content);
                        $('.popup-window-editMenuItem').dialog('close');
                    } else {
						message.errors(res);
                    }
                }
            });
        });
    });
});