$(function() {	
	require(['itemsList'], function() {
		
	});
});
////$(function() {
//	
//	// curator popup
//		$('.curator-cont').each(function() {
//			var cont = $(this);
//			var popup = $('.curator-popup', cont);
//			var timer = 0;
//			$(cont).mouseenter(function(){
//				clearTimeout(timer);
//				timer = setTimeout(function() {
//					popup.stop().fadeIn('slow');
//				}, 400);				
//				return false;
//			}).mouseleave(function(){
//				clearTimeout(timer);
//				popup.stop().fadeOut('fast');
//				return false;
//			});
//			$('.close-popup', popup).click(function(){
//				popup.stop().fadeOut('fast');
//				clearTimeout(timer);
//			});
//		});		
//		$('.actions-panel .action-visability A').click(function() {
//			return false;
//		});
//		
//});