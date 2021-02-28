$(function() {	
require(['ui', 'message'], function(ui, editContent, message) {

		$('.actions-panel .action-save').click(function() {
			$('.content-scroll FORM').ajaxSubmit();
		});
	});
});