$(function() {
	var sendButton=$(".view-content .action-save");
	var robotsForm=$(".robots-form");
	sendButton.click(function() {
		robotsForm.ajaxSubmit();
	});
});


