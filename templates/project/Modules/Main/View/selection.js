$(function() {
	require(['ui'], function(ui) {

		$('.m-bed-ch').on('change', 'INPUT[type="checkbox"], INPUT[type="radio"]', function() {
			var checkBox = $(this);
			var label = checkBox.closest('LABEL');
			if (checkBox.is(':checked')) {
				if (checkBox.data('radio') !== '') {
					$(checkBox.data('radio') + '-radio').not(checkBox).prop('checked', false).closest('LABEL').removeClass('m-current');
				}
				label.addClass('m-current');
			} else {
				label.removeClass('m-current');
			}
		});
		
		$(".request-form .f-dropdown").each(function(){
			ui.dropdown($(".request-form .f-dropdown"),{
				select:function(){
					var cont = $(this).closest('.f-dropdown');
					var title = cont.find('.dropdown-toggle SPAN');
					var text;
					var l = $('INPUT:checked', cont).length ;
					var d = ' ' + (( l > 4) ? title.data('title_five') : title.data('title_two'));
					if ( l > 1) {
						title.text(l + d);
					} else if( l == 0) {
						title.text(title.data('title'));
					} else {
						text = $('INPUT:checked', cont).next().find('SPAN').text();
						text.length > 16 ? text = text.substr(0,16) + '...': '';
						title.text(text);
					}
					return false;
				},
			});
		});
		$(".request-form .dropdown-menu").each(function(){
			if ($('.dropdown-menu LI').length >  5){
				$(this).mCustomScrollbar({
					mouseWheel:{ scrollAmount: 30}	,
					scrollInertia: 120,
				});
			}
		});

	});
});