require.config({
	baseUrl: "/js/"
});

$(function() {
	require(['../templates/base/script', 'ui', 'poly', 'mapBlock'], function(base, ui, Poly, mapBlock) {
	   
       
       /** check a privacy policy */
        var reqForm = $('.request-form.user-form');
        var agree = reqForm.find('#agree');
        var er = agree.parents('.f-row').find('.f-errors');

        agree.on('change', function() {
        	if ($(this).is(':checked') && er.is(':visible')) {
                er.hide();
			}
		});


        $("body").on('click', ".request-form.user-form BUTTON", function(e){
            if (agree.length !== 0) {
            	console.log(!agree.is(':checked'));
            	if (!agree.is(':checked')) {
                    e.preventDefault();
            		if (er.length !== 0) {
                        er.removeClass('a-hidden');
					}
				}
			}
        });
       

		window.sendGA = function sendGA(category, action, label) {
			if ('yaCounter33436783' in window && yaCounter33436783.reachGoal) {
				yaCounter33436783.reachGoal(category +'.'+ action + ((label != '') ? '.' + label:''));
			}
			if ('_gaq' in window && _gaq.push) {
				_gaq.push(['_trackEvent', category, action, label]);
			}
			if ('ga' in window) {
				ga('send', 'event', category, action, label);
			}
		};
        
		$("body").on('keydown', ".js-request-form INPUT[name='author']", function(){
			sendGA('form_request', 'fill', 'name');
		});
		$("body").on('keydown', ".js-request-form INPUT[name='phone']", function(){
			sendGA('form_request', 'fill', 'phone');
		});
		$("body").on('keydown', ".js-request-form INPUT[name='email']", function(){
			sendGA('form_request', 'fill', 'email');
		});
		$("body").on('change', ".js-request-form INPUT[name='subscr']", function(){
			if ($(this).prop('checked')) {
				sendGA('form_request', 'click', 'subOn');
			} else {
				sendGA('form_request', 'click', 'subOff');
			}

		});
        /*
		$("body").on('click', ".js-request-form BUTTON", function(){
			sendGA('form_request', 'click', 'send');
		});
*/


		$("body").on('keydown', ".js-owner-form INPUT[name='address']", function(){
			sendGA('form_request_owner', 'fill', 'adress');
		});
		$("body").on('keydown', ".js-owner-form INPUT[name='bed_number']", function(){
			sendGA('form_request_owner', 'fill', 'bedroom');
		});
		$("body").on('keydown', ".js-owner-form INPUT[name='area']", function(){
			sendGA('form_request_owner', 'fill', 'area');
		});
		$("body").on('keydown', ".js-owner-form INPUT[name='price']", function(){
			sendGA('form_request_owner', 'fill', 'price');
		});
/*
		$("body").on('click', ".js-owner-form BUTTON", function(){
			sendGA('form_request_owner', 'click', 'send');
		});
*/
		$("body").on('change', ".js-owner-form INPUT[name='species']", function(){
			if ($(this).prop('checked')) {
				sendGA('form_request_owner', 'click', 'viewap_On');
			}
		});


		$("body").on('click', ".js-pdf", function(){
			sendGA('Button', 'click', 'pdf');
		});
		$("body").on('click', ".favorite", function(){
			sendGA('Button', 'click', 'fav');
		});
		$("body").on('click', ".js-search", function(){
			sendGA('Button', 'click', 'search');
		});
        

		var preloader = $('.main-loader');
		setTimeout(function(){
			preloader.addClass('m-loaded');
			preloader.fadeOut();
		},2000);
		
		window.onload = function(){
			preloader.addClass('m-loaded');
			
			$('.delay-img').each(function(){
				$(this).css({
					"background-image": "url(" + $(this).data('bg-img') + ")",
				});
			});
			
		};

		// Адрес
		if ($('.js-address').length) {
            var addressJS;
            var prefix = $('body').data('prefix-url');
            var addressJSUrl = prefix != '' ? prefix : '';
            $.post(window.location.protocol + '//' + window.location.hostname + addressJSUrl + '/main/getAddress/', function(res) {
               $('.js-address').each(function(){
                   if (!res.errors) {
                       $(this).text(res.data.office_address);
                   }
               });
            }, 'json');
		}

		// Формы
		ui.form('.user-form', {
			success: function() {
				$(this).addClass('m-sended');
				$(this).removeClass('m-error');
			},
			errors: function(){
				$(this).addClass('m-error');
			}
		});

		// увеличить qr
		if ($('.qr-wrap').length) {
			$('body').click(function(e){
				if($(e.target).closest('.qr-wrap').length) {
					if ($('.qr-wrap').hasClass('m-active')) {
						$('.qr-wrap').removeClass('m-active');
					} else {
						$('.qr-wrap').addClass('m-active');
					}
				} else {
					$('.qr-wrap').removeClass('m-active');
				}
			});
		}

		// полигоны
		$('.poly-scheme').each(function() {
			if (!$(this).data('coords')) return;
			var coords = $(this).data('coords');
			coords = coords.split('|');
			new Poly($(this), {}, function() {
				var poly = this;
				for (var i in coords) {
					poly.add({
						coords: coords[i],
						'stroke-width': 0,
						cursor: 'default',
						fill: '#ff7e00',
						opacity: 0.5
					});
				}
			});
		});

		//add to favorite
		var favIcon = $('.favorite-icon');

		var checkFavIcon = function(num) {

      if ( !favIcon.hasClass('changed') ) {
        favIcon.addClass('changed');
        setTimeout(function(){
          favIcon.addClass('no-trans-fav').removeClass('changed');
          setTimeout(function(){
             favIcon.removeClass('no-trans-fav');
          },10);
        },150);
      }

			if (typeof num === undefined) num = $('.num', favIcon).text();
			num = parseInt(num);
			if (num) favIcon.addClass('m-shown');
			else favIcon.removeClass('m-shown');
			$('.num', favIcon).html('<span>'+num+'</span>');
		};
		$('BODY').on('click', '.favorite, .favorite-btn', function(e){
			var btn = $(this);
			var id = btn.data('id');
			var url = '/' + btn.data('url');

      e.preventDefault();

      if ( !btn.hasClass('waiting') ) {
        btn.addClass('waiting');
        setTimeout(function(){
          btn.addClass('no-trans').removeClass('waiting');
          setTimeout(function(){
             btn.removeClass('no-trans');
          },10);
        },400);
      }

      if ( btn.data('progress') ) return;

      btn.data('progress',true);
      var $thisBtn = $('.favorite, .favorite-btn').filter('[data-id='+id+']');




      if (btn.hasClass('m-added')) {

        $thisBtn.removeClass('m-added');

         $.post(url + '/removeFromFavorites/', {entity_id: id}, function(res) {
           if (res.data) checkFavIcon(res.data.favorites_count);
           if (res.status !== 'ok') {
             $thisBtn.addClass('m-added');
           }
           btn.data('progress',false);
         }, 'json');

      } else {

        $thisBtn.addClass('m-added');

        $.post(url +'/addToFavorites/', {entity_id: id}, function(res) {
          if (res.data) checkFavIcon(res.data.favorites_count);
          if (res.status !== 'ok') {
            $thisBtn.removeClass('m-added');
          }
          btn.data('progress',false);
        }, 'json');

      }

		});

		var selectDistr = function(el){
			if (!el.length) return;
			var cont = el.closest('.f-dropdown');
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
				text.length > 13 ? text = text.substr(0,12) + '...': '';
				title.text(text);
			}
			return false;
		};
		//selectDistr($('.filter .f-dropdown'));
		$(".filter .f-dropdown").each(function(){
			ui.dropdown($(".filter .f-dropdown"),{
				select: function(){
					selectDistr($(this));
					return false;
				}
			});
		});
		$(".filter .dropdown-menu").each(function(){
			if ($('.dropdown-menu LI').length >  5){
				$(this).mCustomScrollbar({
					mouseWheel:{ scrollAmount: 30}	,
					scrollInertia: 120,
				});
			}
		});

		//search
		var searchWrap = $('.search-wrap');
		var form = $('.request-form', searchWrap);
		var searchInput = $('#search');
		var searchUrl = $('#search').data('url');
		var resultWrap  = $('#search-autocomplete-result');
		var fndBtn = $('BUTTON.btn', searchWrap);
		var wtCover = $('.white-cover');
		var searchDd = $('.header-big.dropdown-search');
		searchInput.on('touchstart', function(){
			searchInput.focus();
		});
		ui.dropdown(searchDd,{
			select:function(){
				return false;
			},
			open: function(){
                searchInput.focus();
			},
            close: function(){
                searchWrap.css({"left":"-99999px"});
            },
			beforeOpen: function(){
                searchWrap.css({"left":"0"});
				wtCover.fadeIn(100);
			},
			beforeClose: function(){
				wtCover.fadeOut(100);
                searchInput.blur();
			}
		});

		$('BUTTON', searchWrap).click(function(){
			if( searchInput.val().length < 2 ) {
				return false;
			}
		});
		$(searchDd).on('click', '.close', function() {
			ui.dropdown.close(searchDd);
		});

		//autocomplete

		searchInput.on('keyup change', function(e){
			//console.log('asasasasasa');
			if (searchInput.val().length > 1) {
				$('.search-loader', searchWrap).fadeIn(100);
				$.post(searchUrl, {phrase: searchInput.val()}, function(res) {
					resultWrap.html(res.content);
					console.log(res);
					$('.search-loader', searchWrap).fadeOut(100);
				}, 'json');
				fndBtn.addClass('m-active');
			} else if (searchInput.val().length == 0 ) {
				resultWrap.html('');
				fndBtn.removeClass('m-active');
			};
//			if ( e.which == 40 ) { // down arrowkey
//				$('.object-row', resultWrap).focus();
//				console.log('down');
//			}
//			if ( e.which == 38 ) { // UP arrowkey
//				console.log('up');
//			}
		});

		$('.prevent-scroll').on('mousewheel', function(e) {
			if (!$(e.target).closest('.prevent-scroll').is(this)) return true;
			e = e.originalEvent;
			var el = $(this)[0];
			var dir = e.deltaY || e.detail || -e.wheelDelta;
			var scroll = el.scrollTop;
			var bottom = el.scrollHeight - el.clientHeight - scroll;
			if ((dir < 0 && scroll <= 1) || (dir > 0 && bottom <= 1)) {
				return false;
			}
		});
		$('body').on('click', '.cover-controls', function(){
			return false;
		});


    (function(){
      $('.bow_tie').click(function(){
        var el = $(this);
        if( !$(this).hasClass('jello') ) {
          el.addClass('jello');
          setTimeout(function(){
            el.removeClass('jello');
          },600);
        }
      });
    })();
//		if ($('body').hasClass('m-mobile')) {
//			var btn = $('.favorite-icon');
//			function btnResize(){
//				setTimeout(function() {
//					var h = window.innerHeight / 17;
//					btn.css({
//						height: h + 'px',
//						width: h + 'px',
////						"margin-left": (-h/2) + 'px',
//						'top': window.pageYOffset + window.innerHeight*0.97 - btn.height() + 'px',
//						'right': window.pageXOffset + window.innerWidth/2  + 'px',
//					});
//				}, 100);
//			}
//			btnResize();
//
//			$(window).scroll(function(){
//				btnResize();
//			});
//		}
	});
});