$(function() {
	require(['ui', 'carousel', 'equalCol', "collapsableBoxes", 'popupFilter', "slider"], function(ui, carousel, equalCol, collapsableBoxes, popupFilter) {	

		/*********************Collapsable boxes*****/
		collapsableBoxes.init({
			closed:[".m-pay-variants"]
		});
  
		/********************Equal columns********/
		equalCol($(".single-item"));
		
		$(window).load(function() {
			$(".categories-wrapper .inner-wrapper").each(function() {
				var $target=$(this);
				var $catCollection=$(".single-category", $target);
				equalCol($catCollection);
			});
		});

	});
});