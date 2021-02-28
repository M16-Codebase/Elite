$(function() {
	require(['ui', 'editContent'], function(ui, editContent) {
		
		$('.actions-panel .action-save').click(function() {
			var form = $(this).closest("FORM");
			ui.form.submit(form, {
				url: "/seo/counters/",
				success: function() {
				
				}
			});
			return false;
		});
		ui.slidebox('.wblock',{
			body: '.white-inner-cont',
			open: function() {$(window).resize();},
			close: function() {$(window).resize();}
		});
		// слайдбокс 
		$('.allow-variants').on('click', function(){
			var slideEl = $(this).closest('.wblock');
			if ($(this).prop('checked')) {
				ui.slidebox.open(slideEl);
			} else {
				ui.slidebox.close(slideEl);
			}
		});
		
		ui.slidebox('.g-slidebox',{
			body: '.g-slide-body',
			open: function() {$(window).resize();},
			close: function() {$(window).resize();}
		});
		$('.allow').on('click', function(){
			var slideEl = $(this).closest('.g-slidebox');
			
			if ($(this).prop('checked')) {
				ui.slidebox.open(slideEl);
			} else {
				ui.slidebox.close(slideEl);
			}
		});
		
	});
});