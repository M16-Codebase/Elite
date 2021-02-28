$(function() {
	require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {
		
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-redirect',
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
			var target = $(this);
			var parent = target.closest(".white-block-row");
			var urlTo = parent.find('.view_to').text();
			var urlFrom = parent.find('.view_from').text();
			editContent.open({
				getform: '/seo/editRedirect/',
				getformtype: 'json',
				getformdata: {
					fr: urlFrom,
					to: urlTo
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

		//удалить ссылку
		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var parent = $(this).closest('.white-block-row');
			message.confirm({
				text: 'Подтвердите удаление редиректа.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/seo/deleteRedirect/',
						type: 'post',
						dataType: 'json',
						data: {from: $('.view_from', parent).text()},
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
		
		//фильтр
//		$('.white-body').on('click','.action-filter', function() {
//			var from = $(this).closest('redirects-forms').find('INPUT[name=from]');
//			var to = $(this).closest('redirects-forms').find('INPUT[name=to]');
//			$.ajax({
//				url: '/seo/redirectList/',
//				type: 'post',
//				dataType: 'json',
//				data: {from: from, to: to},
//				success: function(res){
//					if (res.errors === null){
//						$('.white-body').html(res.content);
//						$(window).resize();
//					} else {
//						message.errors(res);
//					}
//				}
//			});
//
//		});	

	});
});
////$(function(){
//	
//	//variables
//	var editForm=$(".edit-content .edit-redirect");
//		var fromVisibleEdit=$('INPUT[name="fr_visible"]', editForm);
//		var fromEdit=$('INPUT[name="fr"]', editForm);
//		var toEdit=$('INPUT[name="to"]', editForm);	
//		var editBackButton=$(".edit-content .action-back");
//	var createForm=$(".edit-content .add-redirect");	
//		var createBackButton=$(".add-redirect .action-back");
//		var fromCreate=$('INPUT[name="fr"]', createForm);
//		var toCreate=$('INPUT[name="to"]', createForm);	
//		
//	var deleteButton=$(".action-delete");
//	
//	//common functions
//		function showForm(form) {
//			form.show().siblings().hide();
//			$('.main-content-inner').addClass('m-edit');		
//		}
//
//		function validateFields(form) {
//			var inputTo=$('INPUT[name="to"]', form);
//			var inputFrom=$('INPUT[name="fr"]', form);
//			return (inputTo.val() == '' || inputFrom.val() == '') ? false : true;		
//		}
//		
//		function cleanFields(fieldsArray) {
//			for(var i=0; i<fieldsArray.length; i++) {
//				fieldsArray[i].val(" ");
//			}
//		}
//	
//	//events
//	
//		//show create form
//		$('.actions-panel .action-add').click(function(){
//			showForm(createForm); 
//		});
//
//		//show edit form
//		$(".white-block-row .action-edit").click(function() {
//			var target=$(this);
//			var parent=target.closest(".white-block-row");
//			var urlTo=parent.find('.view_to').text();
//			var urlFrom=parent.find('.view_from').text();
//			fromVisibleEdit.val(urlFrom);
//			fromEdit.val(urlFrom);
//			toEdit.val(urlTo);		
//			showForm(editForm);		
//		});
//
//		editBackButton.click(function() {
//			cleanFields([fromVisibleEdit, fromEdit, toEdit]);
//		});
//		
//		createBackButton.click(function() {
//			cleanFields([fromCreate, toCreate]);
//		});
//
//		//validate create form
//		createForm.bind("submit", function() {
//			var validation=validateFields(createForm);
//			if(!validation) {
//				alert("Все поля должны быть заполнены!");
//				return false;
//			}		
//		});	
//
//		//validate edit form
//		editForm.bind("submit", function() {
//			var validation=validateFields(editForm);
//			if(!validation) {
//				alert("Все поля должны быть заполнены!");
//				return false;
//			}		
//		});
//		
//		//delete button
//		deleteButton.click(function() {
//			if (!confirm('Удалить?')) return false;
//			var parent = $(this).closest('.white-block-row');
//			$.post('/seo/deleteRedirect/', {from: $('.view_from', parent).text()}, function(result){
//				if (result == '') {
//					window.location.reload();
//				} else {
//					alert(result);
//				}
//			});			
//		});
//		
//		
//		//exp
//		
////		$(".action-button.action-save.send-edit").click(function() {
////			editForm.ajaxSubmit();
////		});
//	
//	
//	
//    /*$('.seo-header .create').click(function(){
//        if (!$(this).hasClass('unactive')){
//            $('.popup-create-redirect').dialog({
//                title: 'Создание',
//                width: 500
//            }).dialog('open');
//        }
//        return false;
//    });*/
//    
//    /*$('.popup-create-redirect .submit-button').click(function(){
//        var form = $(this).closest('FORM');
//        if ($('INPUT[name="to"]', form).val() == '' || $('INPUT[name="fr"]', form).val() == '') {
//            alert('Значения не должны быть пустые');
//        } else {
//            form.submit();
//        }
//        return false;
//    });*/
//    
////    $('.ribbed .edit').click(function() {
////        var tr = $(this).closest('TR');
////        $('.popup-edit-redirect INPUT[name="fr_visible"]').val($('.view_from', tr).text());
////        $('.popup-edit-redirect INPUT[name="fr"]').val($('.view_from', tr).text());
////        $('.popup-edit-redirect INPUT[name="to"]').val($('.view_to', tr).text());
////        $('.popup-edit-redirect').dialog({
////            title: 'Редактирование',
////            close: function(){
////                $('.popup-edit-redirect .from_text').text('');
////                $('.popup-edit-redirect INPUT[name="to"]').val('');
////            }
////        }).dialog('open');
////        return false;
////    });
//    
////    $('.popup-edit-redirect .submit-button').click(function() {
////        var form = $(this).closest('FORM');
////        if ($('INPUT[name="to"]', form).val() == '' || $('INPUT[name="fr"]', form).val() == '') {
////            alert('Значения не должны быть пустые');
////        } else {
////            form.submit();
////        }
////        return false;
////    });
//    
////    $('.ribbed .delete').click(function() {
////		if (!confirm('Удалить?')) return false;
////        var tr = $(this).closest('TR');
////        $.post('/seo/deleteRedirect/', {from: $('.view_from', tr).text()}, function(result){
////            if (result == '') {
////                window.location.reload();
////            } else {
////				alert(result);
////			}
////        });
////        return false;
////    });
//});