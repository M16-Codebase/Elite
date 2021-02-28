$(function(){
	
	//variables
	var toImport=$(".to-import");
	var importForm=$(".import-csv-form");
	var saveImport=$(".save-import");
	var renameForm=$(".rename-form");
	var saveRename=$(".save-rename");

	
	/*common functions*/
	function showForm(form) {
		form.show().siblings().hide();
		$('.main-content-inner').addClass('m-edit');		
	}
	
	/*events*/
	toImport.click(function(e) {
		e.preventDefault();
		showForm(importForm);
	});
	
	//check/uncheck access
    $('.user_permission_checkbox').change(function(){
        var permission = 1;
        if (this.checked){
            permission = 1;
        }else{
            permission = 0;
        }
        $.post('/permissions/setPermission/', {
            role_id: $(this).data('role_id'),
            action_id: $(this).data('action_id'),
            permission: permission
        }, function(result){
            //если ошибка, отобразить, поменять галку обратну.
        });
    });
	
	//rename or set name for action
    $('.permissions-container .rename-actions-btn').click(function(evt){
        evt.preventDefault();
        var btn = $(this);
		showForm(renameForm);
        $('input[name="id"]', renameForm).val(btn.data('id'));
        $('input[name="title"]', renameForm).val(btn.data('title'));
    });
	
	//save import
	saveImport.click(function(e) {
		e.preventDefault();
		importForm.ajaxSubmit({
			type:"POST",
			dataType:"json",
			success:function(res) {
				var errors=res.errors;
				if(errors) {
                    // только одна ошибка - errors.file == empty
					console.log(errors);
				} else {
					//window.location.reload();
				}
			}
		});
	});
	
	//save renamed
	saveRename.click(function(e) {
		var fromRename=$(this).closest("ul.actions-panel").find(".rename-close");
		e.preventDefault();
		renameForm.ajaxSubmit({
            dataType: 'json',
            success: function(res){
                if (res.errors === null){
                    $('#action-' + res.data.id + ' .action-title').text(res.data.title);
					fromRename.trigger("click");
                }
            }
        });
	});

//    $('.actions-panel .action-import').click(function(evt){
//        evt.preventDefault();
//        $('.popup-window-import-permissions').dialog({
//            title: 'Импорт прав доступа из CSV-файла'
//        }).dialog('open');
//    });	
	
//    $('.popup-rename-action form').submit(function(evt){
//        evt.preventDefault();
//        $(this).ajaxSubmit({
//            dataType: 'json',
//            success: function(res){
//                if (res.errors === null){
//                    $('#action-' + res.data.id + ' .action-title').text(res.data.title);
//                }
//            }
//        });
//    });
});