$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {

		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.add-form',
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
			return false;
		});
		
		
		$('.white-body').on('click', '.action-edit', function() {
			var id = $(this).closest('.wblock').data('id');
			editContent.open({
				getform: '/seo-sitemap/editRuleFields/',
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
			var delUrl = "/seo-sitemap/deleteRule/";
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
			var itemsForm = $('.sitemap-list') ;
			if ($(this).hasClass('m-inactive')) return false;
			message.confirm({
				text: 'Подтвердите удаление выбранных позиций.',
				type: 'delete',
				ok: function() {
					ui.form.submit(itemsForm, {
						url: '/seo-sitemap/deleteRule/',
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
		
//		$('.actions-panel .action-add').click(function(){
//			$('.edit-content .add-form').show().siblings().hide();
//			$('.main-content-inner').addClass('m-edit');
//			return false;
//		});
//		
//		//variables
//		var saveButton=$(".edit-content .action-save");
//		var linkField=$(".edit-content .link-field");
//		var globalDelete=$(".view-content .action-delete");
//		var sitemapList=$('.sitemap-list');
//		
//		/*events*/
//			
//		//add rule
//		saveButton.click(function(e) {
//			e.preventDefault();
//			var target=$(this);
//			var form=target.closest("form");
//			form.ajaxSubmit({
//				beforeSend:function() {
//					if(linkField.val() === "") {
//						alert("Введите значение!");
//						return false;
//					}
//				},
//				success:function(res) {
//					var errors=res.errors;
//					if(!errors) {
//						window.location.reload();
//					}
//				}
//			});
//		});	
//		
//		globalDelete.click(function(e) {
//			e.preventDefault();
//			sitemapList.ajaxSubmit({
//				success:function(res) {
//					var errors=res.errors;
//					console.log(errors);
//					if(!errors) {
//						window.location.reload();
//					}					
//				}
//			});
//		});
//		
//		sitemapList.on('click', '.action-edit', function() {
//			var item = $(this).closest('.wblock');
//			var url = $('.url-text', item).text();
//			var allow = $('.allow-text', item).text();
//			$('.url-input', item).val(url);
//			$('.allow-menu', item).attr('data-val', allow).find('.dropdown-toggle').text(allow);
//			item.addClass('m-open').siblings().removeClass('m-open');
//			$(window).resize();
//			return false;
//		}).on('click', '.action-delete', function(e) {
//			e.preventDefault();
//			if (!confirm('Удалить правило?')) return false;
//			var id=$(this).data("id");
//			$.ajax({
//				url:"/seo-sitemap/deleteRule/",
//				type:"POST",
//				data:{
//					"ids":id
//				},
//				success:function(res) {
//					var errors=res.errors;
//					console.log(errors);
//					if(!errors) {
//						window.location.reload();
//					}
//				}
//			});
//		}).on('click', '.action-ok', function() {
//			var item = $(this).closest('.wblock');
//			var url = $('.url-input', item).val();
//			var allow = $('.allow-menu', item).data('val');
//			var form=$(this).closest("form");
//			form.submit();
//		}).on('click', '.action-cancel', function() {
//			$('.sitemap-list .wblock').removeClass('m-open');
//			$(window).resize();
//			return false;
//		}).on('click', '.allow-menu LI', function() {
//			var val = $(this).data('val');
//			$(this).closest('.allow-menu').attr('data-val', val).find('.dropdown-toggle').text(val);
//			ui.dropdown.close($(this).closest('.allow-menu'));
//		});
//		
//		ui.clickOut('.wblock .action-edit, .open-head', function() {
//			$('.sitemap-list .wblock').removeClass('m-open');
//			$(window).resize();
//		});
		
	});
});