$(function() {
	require([], function() {
//		$('.popup-errors').dialog({
//			title: 'Ошибка'
//		}).dialog('open');
//		
//		$('.actions-panel .action-add').click(function() {
//			$('.popup-create-siteprop').dialog({
//				title: 'Новый параметр'
//			}).dialog('open');
//			return false;
//		});
//
//		$('.popup-create-siteprop FORM').ajaxForm({
//			success: function(result) {
//				if (result != 'ok') {
//					alert(result);
//				} else {
//					window.location.reload();
//				}
//			}
//		});
//
//		$('.actions-panel .action-save').click(function() {
//			$('#site_config_form').submit();
//			return false;
//		});
	
	/*variables*/
	var globalSave=$(".view-content .action-save");
	var siteConfigForm=$("#site_config_form");
	var saveEdit=$(".edit-content .action-save");
	var addPropForm=$(".add-form");
	
	/*events*/
	
		//show sliding panel
		$('.actions-panel .action-add').click(function(){
			$('.main-content-inner').addClass('m-edit');
			return false;
		});
	
		//create new seo param
		saveEdit.click(function() {
			addPropForm.ajaxSubmit({
				success:function(result) {
					if (result != 'ok') {
						alert(result);
					} else {
						window.location.reload();
					}			
				}
			}); 
		});
		
		//save seo options
		globalSave.click(function() {
			siteConfigForm.submit();
		});
	
	});
});