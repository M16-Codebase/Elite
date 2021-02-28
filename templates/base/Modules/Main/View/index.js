$(function() {
	require(['ui'], function(ui) {	
		
		// presentation
		var minHeight = 550;
		var changeTimer = 0;
		var cont = $('.presentation');
		var car = $('.car-wrap', cont);
		var prev = $('.car-prev', cont);
		var next = $('.car-next', cont);
		var setChange = function() {
			clearInterval(changeTimer);
			changeTimer = setInterval(function() {
				if (car.hasClass('m-no-right')) car.carousel(0);
				else car.carousel('+1');
			}, 6000);
		};
		car.carousel({scroll: function(i) {
			$('.promo-title').eq(i).addClass('m-current').siblings().removeClass('m-current');
			if (car.hasClass('m-no-left')) prev.addClass('m-inactive');
			else prev.removeClass('m-inactive');
			if (car.hasClass('m-no-right')) next.addClass('m-inactive');
			else next.removeClass('m-inactive');
			setChange();
		}});
		$('.promo-title').click(function() {
			if ($(this).hasClass('m-current')) return false;
			car.carousel($(this).index());
			return false;
		});
		$('.presentation .promo-images LI').each(function() {
			var item = $(this);
			var cont = $('.presentation');
			var img = $('IMG', this);
			var imgW = img.width();
			var checkSizes = function() {
				car.carousel($('.promo-title.m-current').index());
				var contW = cont.width();
				item.width(contW);
				if (imgW < contW*0.9) {
					checkSizes();
				} else {
					img.css({
						left: (contW - imgW)/2
					});
				}					
			};
			if (item.height() < minHeight) minHeight = item.height();
			$(window).on('load resize', checkSizes);
			checkSizes();
		});
		setChange();
		$('.presentation .promo-images').height(minHeight);

        $('#autocomplete-form').submit(function(evt){
            evt.preventDefault();
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function(res){
                    if (res.errors === null){
                        $('#autocomplete-result').html(res.content);
                    }
                }
            })
        })
		
	});	
});

function uLoginCallback(token){
    console.log(token);
    $.ajax({
        url: '/login/',
        type: 'post',
        data: {
            ajax: 1,
            token: token
        },
        dataType: 'json',
        success: function(res){
            console.log(res);
        }
    })
}