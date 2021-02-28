define(['ui', 'message', 'editContent', 'imgpreview'], function(ui, message, editContent, imgpreview) {
	var bannersList = $('.banners-list');
	var catUrl;
	var tabs = 0;
	var urlsCount;
	var bannerCurrentUrl = $('.choose-url SELECT').attr('selected', true).val();
	$('.tabs-cont').length ? tabs = 1 :'';
	var customSlidebox = function(){
		ui.slidebox(".custome-slidebox",{
			open: function() {
				urlsCount = $(this).find('.slide-header').text();
				$(this).find('.slide-header').text('Свернуть');
				$(window).resize();
			},
			close: function() {
				$(this).find('.slide-header').text(urlsCount);
				$(window).resize();
			},
			speed:0,
		});
	};
	customSlidebox();
	
	// выбор страницы с баннерами
		$("body").on('change', '.choose-url SELECT', function() {
			var bannerCurrentUrl = $(this).val();
            var segment_id = $('option:selected', $(this)).data('segment_id');
			$.post('/site-banner/banners/', {url: bannerCurrentUrl, segment_id: segment_id}, function(res) {
				if (res.errors) {
					message.errors(res);
				} else {
					if (window.history) {
						history.replaceState({}, '', '/site-banner/' + '?url=' + bannerCurrentUrl + (segment_id ? '&segment_id=' + segment_id : ''));
					}	
					bannersList.html(res.content);
					if (tabs) {
						$('.tabs .count-banners').text($('.wblock:not(.white-header)', bannersList).length);
					}
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			}, 'json');
		});

			
	
		// добавить баннер
		$('#banners .action-button.action-add').click(function() {
			var url_selector = $('.choose-url SELECT');
			var bannerCurrentUrl = url_selector.val();
			var segment_id = $('option:selected', url_selector).data('segment_id');
			if (tabs) {
				catUrl = $(this).closest('.tab-page').data('cat-url');
			} 
			editContent.open({
				getform: '/site-banner/bannerFields/',
				getformtype: 'json',
				getformdata: {
					caturl: catUrl,
					url: bannerCurrentUrl,
					segment_id: segment_id
				},
				loadform: function() {
					imgpreview();
					dropdown();
					ui.datepicker($('#from'), {
						onClose: function( selectedDate ) {
							$('#to').datepicker( "option", "minDate", selectedDate );
						},
						maxDate: $('#to').val()
					});
					ui.datepicker($('#to'), {
						onClose: function( selectedDate ) {
							$('#from').datepicker( "option", "maxDate", selectedDate );
						},
						minDate: $('#from').val()
					});
				},
				success: function(res) {
					bannersList.html(res.content);
					if (tabs) {
						$('.tabs .count-banners').text($('.wblock:not(.white-header)', bannersList).length);
					};
					editContent.close();
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			});
			return false;
		});
		
		// редактировать баннер
		bannersList.on('click', '.banner .action-edit', function() {
			if (tabs) {
				catUrl = $(this).closest('.tab-page').data('cat-url');
			} 
            var bannerLine = $(this).closest('.banner');
			editContent.open({
				getform: '/site-banner/bannerFields/',
				getformtype: 'json',
				getformdata: {
					id: bannerLine.data('id'), 
					url: bannerLine.data('url'),
					segment_id: bannerLine.data('segment_id'),
					caturl: catUrl
				},
				errorsText: {
					"active:cant_activate": "Невозможно отобразить баннер, так как истек срок показа"
				},
				loadform: function() {
					imgpreview();
					dropdown();
					ui.datepicker($('#from'), {
						onClose: function( selectedDate ) {
							$('#to').datepicker( "option", "minDate", selectedDate );
						},
						maxDate: $('#to').val()
						
					});
					ui.datepicker($('#to'), {
						onClose: function( selectedDate ) {
							$('#from').datepicker( "option", "maxDate", selectedDate );
						},
						minDate: $('#from').val()
					});
//					ui.initAll();
				},
				success: function(res) {
					bannersList.html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			});
			return false;
		});
		
		// удалить баннер
		bannersList.on('click', '.banner .action-delete', function() {
			var bannerLine = $(this).closest('.banner');
			message.confirm({
				text: 'Подтвердите удаление баннера.',
				type: 'delete',
				ok: function() {
					$.post('/site-banner/delete/', {id: bannerLine.data('id'), url: bannerLine.data('url'), segment_id: bannerLine.data('segment_id'), cat: tabs}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							bannersList.html(res.content);
							if (tabs) {
								$('.tabs .count-banners').text($('.wblock:not(.white-header)', bannersList).length);
							};
							$(window).resize();
							ui.initAll();
							customSlidebox();
						}
					}, 'json');
				}
			});
			return false;
		});
		
		// видимость баннера
		bannersList.on('click', '.banner .action-visibility', function() {
			var btn = $(this);
            var bannerLine = $(this).closest('.banner');
			var active = $(this).hasClass('action-show')? 0 : 1;
			$.post('/site-banner/activate/', {
				active: active,
                id: bannerLine.data('id'),
                url: bannerLine.data('url'),
                segment_id: bannerLine.data('segment_id')
			}, function(res) {
				if (res.errors) {
					if (res.errors.active == 'cant_activate') {
						message.errors("Невозможно отобразить баннер, так как истек срок показа");
					} else {
						message.errors(res);
					}
				} else {
					btn.removeClass('action-show action-hide').addClass(active? 'action-show' : 'action-hide').attr('title', active? 'Показан' : 'Скрыт');
					$('I', btn).removeClass('icon-show icon-hide').addClass(active? 'icon-show' : 'icon-hide');
				}
			}, 'json');
			return false;
		});
		
		//видимость баннера во время редактирования
		var classArray = {"1":"show","0":"hide"};
		var dropdown = function(){
			ui.dropdown($('.banner-show-dropdown'),{
				select: function() {
					var selectItem = $(this);
					var dropTgl = selectItem.closest('.dropdown-menu').prev();
					var type = selectItem.data('type');
					selectItem.closest('.dropdown').find('INPUT[name="active"]').val(type);
					dropTgl.find('span').text(selectItem.text());
					dropTgl.find('i').attr("class", "icon-" + classArray[type]);
				}
			});
		};
		
		// сортировка баннера
		bannersList.on('click', '.banner .action-sort', function() {
            var bannerLine = $(this).closest('.banner');
			$.post('/site-banner/switchSortMode/', {
				id: bannerLine.data('id'), 
				url: bannerLine.data('url'),
				cat: tabs,
				segment_id: bannerLine.data('segment_id')
			}, function(res) {
				if (res.errors) {
					message.errors(res);
				} else {
					bannersList.html(res.content);
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			}, 'json');
			return false;
		});
		bannersList.on('ui-sortable-sorted', function() {
			$(window).resize();
			ui.initAll();
		});
		
		// список страниц для баннера
		$('BODY').on('click', '.edit-banner .url-list .add-btn', function() {
			var origin = $(this).closest('.row').siblings('.origin');
			var newRow = origin.clone().removeClass('a-hidden origin').hide();
			newRow.insertBefore(origin).slideDown(300, function() {
				$(window).resize();
			});
			return false;
		}).on('click', '.edit-banner .url-list .delete-item', function() {
			var delItem = $(this).closest('.banner-link');
			message.confirm({
				text: 'Подтвердите удаление ссылки.',
				ok: function() {
					delItem.slideUp(300, function(){
						delItem.remove();
					});
				}
			});
			return false;
		});
}); 
