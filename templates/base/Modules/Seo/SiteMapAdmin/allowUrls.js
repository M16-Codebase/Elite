$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {

		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-allow-url',
				clearform: true,
				loadform:function(){
					 $(".datepicker-init", this).removeClass("datepicker-init").addClass("datepicker");
					  ui.initAll();
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
			return false;
		});
		
		
		$('.white-body').on('click', '.action-edit', function() {
			var id = $(this).closest('.wblock').data('id');
			editContent.open({
				getform: '/seo-sitemap/editAllowUrlFields/',
				getformdata: {id: id},
				getformmethod: 'post',
                getformtype: 'json',
				success: function(res) {
					if (!res.errors){
						$('.white-body').html(res.content);
						editContent.close();
						$(window).resize();
						ui.initAll();
					} else {
						message.errors(res);
					}
				}
			});
			return false;
		});
		
		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.wblock').data('id');
			var delUrl = "/seo-sitemap/deleteAllowUrls/";
			message.confirm({
				text: 'Подтвердите удаление правила.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url:  delUrl,
						type: 'post',
						data: {ids: id},
						dataType: 'json',
						success: function(res){
							if (!res.errors){
								$('.white-body').html(res.content);
								$(window).resize();
							} else {
								message.errors(res);
							}
						}
					});
				}
			});
			return false;
		});
		
		//удаление выбранных
		$('.view-content').on('click', '.actions-panel .action-delete', function() {
			var itemsForm = $('.link-list') ;
			if ($(this).hasClass('m-inactive')) return false;
			message.confirm({
				text: 'Подтвердите удаление выбранных позиций.',
				type: 'delete',
				ok: function() {
					ui.form.submit(itemsForm, {
						url: '/seo-sitemap/deleteAllowUrls/',
						method: 'POST',
						success: function(res) {
							$('.check-all').prop("checked", false);
							$('.white-body').html(res.content);
						},
						errors: function(errors) {
							message.errors({errors: errors});
						},
						serverError: function(err) {
							message.serverErrors(err);
						}
					});
				}
			});
			return false;
		});
		
	});
});
////$(function() {
//	
//	/*variables*/
//	var addForm=$(".add-allow-url");
//	var addURL=$(".view-content .action-add");
//	var saveAddedUrl=$(".action-save", addForm);
//	var linkList=$("#link-list");
//	var editForm=$(".edit-content .edit-allow-url");
//	var editSubmit=$(".action-save", editForm);
//	var editUrl=$(".edit-content input[name='url']"); 
//	var editSelect=$("select[name='priority']", editForm);
//	var editId=$(".edit-content input[name='id']");
//	var editMod=$(".edit-content input[name='last_modification']");
//	var deleteAll=$(".view-content .actions-panel .action-delete");
//	var deleteForm=linkList;
//	
//	/*common functions*/
//	function showForm(form) {
//		form.show().siblings().hide();
//		$('.main-content-inner').addClass('m-edit');		
//	}
//	
//	//events
//	
//	//show adding form
//	addURL.click(function(e) {
//		e.preventDefault();
//		showForm(addForm);	
//	});
//	
//	//submit adding form
//	saveAddedUrl.click(function(e) {
//		e.preventDefault();
//		addForm.ajaxSubmit({
//			success:function(res) {
//				var errors=res.error;
//				if(errors) {
//					console.log(errors);
//				} else {
//					window.location.reload();
//				}
//			}
//		});
//	});
//	
//	//submit editing form
//	editSubmit.click(function(e) {
//		e.preventDefault();
//		editForm.ajaxSubmit({
//			url:"/seo-sitemap/editAllowUrl/",
//			type:"POST",
//			dataType:"json",
//			data:{
//				id:editId.val(),
//				url:editUrl.val(),
//				priority:editSelect.find("option:selected").val()
//			},
//			success:function(res) {
//				console.log(res);
//				console.log(editSelect.find("option:selected").val());
//				var errors=res.errors;
//				if(errors) {
//					if("urls" in errors) {
//						alert("Заполните необходимые поля!");
//					}				
//				} else {
//					window.location.reload();	
//				}
//			}
//		});
//	});
//	
//	//show edit form
//	linkList.on("click", ".edit-link-btn", function(e) {
//		e.preventDefault();
//		var target=$(this);
//		showForm(editForm);
//		var url=target.data("url");
//		var pri=target.data("pri");
//		var mod=target.data("mod");
//		var id=target.data("id");
//		editUrl.val(url);
//		editUrl.attr("value", url);
//		editSelect.find("option:selected").prop("selected", false);
//		editSelect.find("option[value='"+pri+"']").prop("selected", true);
//		editMod.val(mod);
//		editMod.attr("value", mod);
//		editId.val(id);
//	});
//	
//	//single edit
//	linkList.on("click", ".delete-link-btn", function(e) {
//		e.preventDefault();
//		var target=$(this);
//		var form=target.closest("form");
//		deleteForm.ajaxSubmit({
//			dataType:"json",
//			data:{
//				ids:target.data("id")
//			},
//			success:function(res) {
//				var errors=res.errors;
//				if(errors) {
//					console.log(errors);
//				} else {
//					window.location.reload();
//				}
//			}
//		});
//	});
//	
//	//multiple edit
//	deleteAll.click(function() {
//		var target=$(this);
//		if(target.hasClass("m-inactive")) return;
//		deleteForm.ajaxSubmit({
//			success:function(res) {
//				window.location.reload();
//			}			
//		});
//	});
//	
//})
//
//
