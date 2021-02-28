
$(function() {
	require(['ui'], function(ui) {
		
		$(".rebuild-form").submit(function(e) {
			e.preventDefault();
			$.ajax({
				type:"POST",
				url:"/sphinx-wordforms/rebuildIndex/",
				success:function(res) {
					if(!res.errors) {
						var $cronPopup=$(".cron-task");
						$cronPopup.dialog("open");
						setTimeout(function() {
							$cronPopup.dialog("close");
						}, 3500);
					}
				}
			});
		});
	});
});


