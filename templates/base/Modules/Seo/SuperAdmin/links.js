$(function() {
	require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {
	/*variables*/
	var addButton=$(".content-top .action-add");
	var addLinkForm=$(".add-link-form");
	var editLinkForm=$(".edit-link-form");
	var whiteBlocksEdit=$(".white-blocks", editLinkForm);
	var saveAdd=$(".action-save", addLinkForm);
	var saveEdit=$(".action-save", editLinkForm);
	var backButtonAdd=$(".action-back", addLinkForm);
	var backButtonEdit=$(".action-back", editLinkForm);
	var contentArea=$(".view-content .content-scroll-inner");
	
	/*common functions*/
//	function showForm(form) {
//		form.show().siblings().hide();
//		$('.main-content-inner').addClass('m-edit');		
//	}
//	
//	function cleanFields(form) {
//		var inputs=form.find("input");
//		for(var i=0; i<inputs.length; i++) {
//			inputs.eq(i).val(" ");
//		}
//	}
	
	/*events*/
	
	//создать ссылку
//	addButton.click(function() {
//		showForm(addLinkForm);
//	});
	$('.actions-panel .action-add').click(function() {
		editContent.open({
			form: '.add-links-form',
			clearform: true,
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
		var id = $(this).closest('.white-block-row').data('id');
		editContent.open({
			getform: '/seo/editLinkFields/',
			getformtype: 'json',
			getformdata: {
				id: id
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
//	//редактировать ссылку
//	$('.white-body').on('click', '.action-edit', function(){
//		var id = $(this).closest('.white-block-row').data('id');
//        $.ajax({
//            url: '/seo/editLinkFields/',
//            type: 'post',
//            dataType: 'json',
//            data: {
//                id: id
//            },
//            success: function(res){
//				if (res.errors === null){
//					$('.white-body').html(res.content);
//					$(window).resize();
//				} else {
//					console.log(res.errors);
//				}
//            }
//        });			
//	});
	
	//удалить ссылку
	$('.white-body').on('click', '.action-delete', function(){
        var id = $(this).closest('.white-block-row').data('id');
		message.confirm({
			text: 'Подтвердите удаление перелинковки.',
			type: 'delete',
			ok: function() {
				$.ajax({
					url: '/seo/deleteLink/',
					type: 'post',
					dataType: 'json',
					data: {id: id},
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
		});
	});
	
	//сохранение формы с созданием ссылки
//	saveAdd.click(function() {
//		addLinkForm.ajaxSubmit({
//			dataType: 'json',
//			success: function(res) {
//				if (res.errors === null){
//					contentArea.html(res.content);
//					cleanFields(addLinkForm);
//					backButtonAdd.trigger("click");
//				}
//			}		
//		});
//	});
//	
//	//сохранение формы с редактированием ссылки
//	saveEdit.click(function() {
//		editLinkForm.ajaxSubmit({
//			dataType: 'json',
//			success: function(res) {
//				if (res.errors === null){
//					contentArea.html(res.content);
//					backButtonEdit.trigger("click");
//				}
//			}		
//		});
//	});	
	
//	forms.click(function(evt) {
//        evt.preventDefault();
////        var popup = $(this).parents('.popup-window');
//        $(this).ajaxSubmit({
//            dataType: 'json',
//            success: function(res){
//                if (res.errors === null){
//                    linksList.html(res.content);
//                    //popup.dialog('close');
//                }
//            }
//        });		
//	});
	
//    $('.actions-panel .action-add').click(function(evt){
//        evt.preventDefault();
//        var popup = $('.add-link-popup');
//        $('form', popup).trigger('reset');
//        popup.dialog('open');
//    });

//    $('.popup-window FORM').submit(function(evt){
//        evt.preventDefault();
//        var popup = $(this).parents('.popup-window');
//        $(this).ajaxSubmit({
//            dataType: 'json',
//            success: function(res){
//                if (res.errors === null){
//                    $('#links-list').html(res.content);
//                    popup.dialog('close');
//                }
//            }
//        });
//    });

//    $('#links-list').on('click', '.edit-link-btn', function(evt){
//        evt.preventDefault();
//        $.ajax({
//            url: '/seo/editLinkFields/',
//            type: 'post',
//            dataType: 'json',
//            data: {
//                id: $(this).data('id')
//            },
//            success: function(res){
//                if (res.errors === null){
//                    var popup = $('.edit-link-popup');
//                    $('form', popup).html(res.content);
//                    popup.dialog('open');
//                }
//            }
//        })
//    }).on('click', '.delete-link-btn', function(evt){
//        evt.preventDefault();
//        var id = $(this).data('id');
//        var row = $(this).parents('tr');
//        if (confirm('хотите удалить?')){
//            $.ajax({
//                url: '/seo/deleteLink/',
//                type: 'post',
//                dataType: 'json',
//                data: {id: id},
//                success: function(res){
//                    if (res.errors === null){
//                        row.remove();
//                    }
//                }
//            })
//        }
//    });
	});
});