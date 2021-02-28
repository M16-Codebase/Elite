$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
//		/*variables*/
//		var addRole           = $(".content-top .actions-panel .action-add");
//		var addRoleForm       = $(".add-role-form");
//		var saveAddRole       = $(".action-save", addRoleForm);
//		var editForm          = $(".edit-role-form");
//        var editRoleIdField   = $("input[name='id']", editForm);
//		var editTitleInput    = $("input[name='title']", editForm);
//		var editRedirectInput = $("input[name='after_login_redirect']", editForm);
//		var saveEditForm      = $(".action-save", editForm);
//		var pgContent         = $(".page-content");
//		var editBackButton    = $(".action-back", editForm);
//		var addBackButton     = $(".action-back", addRoleForm);
//
//		/*common functions*/
//		function showForm(form) {
//			form.show().siblings().hide();
//			$('.main-content-inner').addClass('m-edit');		
//		}
//		
//		/*events*/
//		addRole.click(function(e) {
//			e.preventDefault();
//			showForm(addRoleForm);
//		});
//		
//		saveAddRole.click(function(e) {
//			e.preventDefault();
//			addRoleForm.ajaxSubmit({
//				resetForm: true,
//				type: 'POST',
//				success: function(res) {
//					try {
//						var parseJSON = JSON.parse(res);
//						var str = '';
//						for (var key in parseJSON.errors){
//							str += parseJSON.errors[key] + '. ';
//						}
//						alert(str);
//					} catch(e) {
//						pgContent.html(res);
//						addBackButton.trigger("click");
//					}			
//				}
//			});				
//		});
//				
//		saveEditForm.click(function(e) {
//			e.preventDefault();
//			editForm.ajaxSubmit({
//				resetForm:true,
//				type: 'POST',
//				success: function(res) {
//					try {
//						var parseJSON = JSON.parse(res);
//						var str = '';
//						for (var key in parseJSON.errors){
//							str += parseJSON.errors[key] + '. ';
//						}
//						alert(str);
//					} catch(e) {
//						pgContent.html(res);
//						editBackButton.trigger("click");
//					}
//				}				
//			});			
//		});
//		
//		pgContent.on("click", ".action-edit", function(e) {
//			e.preventDefault();
//			var target=$(this);
//			showForm(editForm);
//            editRoleIdField.val(target.data('id'));
//			var title=target.data('title');
//			editTitleInput.val(title);
//			var redirect=target.data('redir');
//			console.log(redirect);
//			editRedirectInput.val(redirect);
//		});
//		
//		pgContent.delegate('.action-delete', 'click', function() {
//			if (!confirm('Удалить роль?')) return false;
//			var parent = $(this).closest('.white-block-row');
//			$.post('/permissions/deleteRole/', {id: parent.data('id')}, function(res) {
//				try {
//					var parseJSON = JSON.parse(res);
//					var str = '';
//					for (var key in parseJSON.errors){
//						str += parseJSON.errors[key] + '. ';
//					}
//					alert(str);
//				} catch(e) {
//					pgContent.html(res);
//				}
//			});
//			return false;		
//		});
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				getform: '/permissions/roleFields/',
				getformtype: 'json',
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
		$('.white-body').on('click', '.action-edit', function(){
			var id = $(this).closest('.white-block-row').data('id');
			editContent.open({
				getform: '/permissions/roleFields/',
				getformdata: {
					id: id
				},
				getformtype: 'json',
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

		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			message.confirm({
				text: 'Подтвердите удаление роли.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: '/permissions/deleteRole/',
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
			});
			return false;
		});
		
	
//		var table = $('.roles-table TBODY');
		
		// создать
//		var addPopup = $('.popup-create-role');
//		$('FORM', addPopup).ajaxForm({
//			type: 'POST',
//			success: function(res) {
//				try {
//					var parseJSON = JSON.parse(res);
//					var str = '';
//					for (var key in parseJSON.errors){
//						str += parseJSON.errors[key] + '. ';
//					}
//					alert(str);
//				} catch(e) {
//					table.html(res);
//					addPopup.dialog('close');
//				}			
//			}
//		});
//		$('.actions-panel .action-add').click(function(){
//			$('FORM', addPopup).clearForm();
//			addPopup.dialog({'width': 550}).dialog('open');
//			return false;
//		});

		// редактировать
//		var editPopup = $('.popup-update-role');
//		$('FORM', editPopup).ajaxForm({
//			type: 'POST',
//			success: function(res) {
//				try {
//					var parseJSON = JSON.parse(res);
//					var str = '';
//					for (var key in parseJSON.errors){
//						str += parseJSON.errors[key] + '. ';
//					}
//					alert(str);
//				} catch(e) {
//					table.html(res);
//					editPopup.dialog('close');
//				}
//			}
//		});
//		table.delegate('.reload', 'click', function() {
//			var tr = $(this).closest('TR');
//			$('FORM', editPopup).clearForm();
//			$('INPUT[name="id"]', editPopup).val(tr.data('id'));
//			$('INPUT[name="title"]', editPopup).val(tr.data('title'));
//			editPopup.dialog('open');
//			return false;
//		});
//
//		// удалить
//		table.delegate('.delete', 'click', function() {
//			if (!confirm('Удалить роль?')) return false;
//			var tr = $(this).closest('TR');
//			$.post('/permissions/deleteRole/', {id: tr.data('id')}, function(res) {
//				try {
//					var parseJSON = JSON.parse(res);
//					var str = '';
//					for (var key in parseJSON.errors){
//						str += parseJSON.errors[key] + '. ';
//					}
//					alert(str);
//				} catch(e) {
//					table.html(res);
//				}
//			});
//			return false;
//		});
	
	});
});