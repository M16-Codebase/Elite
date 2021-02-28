require.config({baseUrl: "/templates/base/Admin/js/"});
$(function() {
	require(['ui', 'message', 'editContent', 'actionsPanel', 'filter', 'picUploader', 'asideMenu'], function(ui, message, editContent, actionsPanel, filter) {
				
		// поиск в шапке
		$('.header-search').submit(function() {
			if (!$('.search-input', this).val()) return false;
		});
		
		// высота контента
		(function() {
			var header = $('.page-header').outerHeight(true);
			var bc = $('.breadcrumbs').outerHeight(true);
			var main = $('.main-content.main-col');
			var mainInner = $('.main-content-inner');
			var scrollOn = false;
			var mobile = false;
			if (bc) bc+= 8;
			var setHeight = function() {
				var wHeight = $(window).height();
				var message = $('.message-errors')[0].offsetHeight;
				if ($('.mobile-detect').width()) mobile = true;
				else mobile = false;
				actionsPanel();
				if (mobile) {
					if (scrollOn) $('.viewport').mCustomScrollbar('destroy');
					mainInner.css({
						height: (wHeight - header - bc - message),
						width: $('.page-main').width()
					});
					$('.viewport').css({height: 'auto'});
					main.css({width: '100%'}).removeClass('m-fixed');
					scrollOn = false;
				} else {
					var content = $('.content-scroll');
					content.each(function() {
						if ($(this).is('.a-hidden') || $(this).closest('.a-hidden').length) return;
						var tabTop = $(this).find('.tab-top').outerHeight(true);
						tabTop += $(this).siblings('.tab-top').outerHeight(true);
						var contentTop = $(this).siblings('.content-top').outerHeight(true);
						contentTop += $(this).closest('.content-scroll-cont').siblings('.content-top').outerHeight(true);
						$('.viewport', this).css({
							height: (wHeight - header - contentTop - tabTop - bc - message - 46)
						});
						if (!$('.viewport', this).hasClass('mCustomScrollbar')) {
							$('.viewport', this).mCustomScrollbar({
								scrollInertia: 120,
								//mouseWheel: {scrollAmount: 240},
								advanced: {autoScrollOnFocus: false},
								callbacks: {
									whileScrolling: function() {
										$(this).trigger('mScrolling');
										$(this).closest('.content-scroll').scrollTop(0);
									}
								}
							});
							scrollOn = true;
						}
					});
					mainInner.css({height: 'auto', width: 'auto'});
					main.css({width: $('.page-main').width()}).addClass('m-fixed');
				}
			};
			$('.content-scroll').on('scroll resize', function() {
				$(this).scrollTop(0);
			});
			$(window).on('resize', setHeight).on('mousewheel', function(e) {
				$(window).scroll();
				var target = $(e.target);
				if (target.is('.redactor-box textarea') || target.closest('.redactor-box textarea').length) {
				} else if (target.is('.viewport') || target.closest('.viewport').length) return;
				if (target.is('.page-aside') || target.closest('.page-aside').length) return;
				if ($('.content-scroll .redactor-act.dropact').length) {
					if (target.is('.redactor-dropdown') || target.closest('.redactor-dropdown').length) {
						var dd = target.is('.redactor-dropdown')? target : target.closest('.redactor-dropdown');
						dd.scrollTop(dd.scrollTop() + (e.deltaY > 0? -50 : 50));
					}
					return;
				};
				$('.content-scroll').each(function() {
					if ($(this).closest('.a-hidden').length) return;
					if ($(this).closest('.edit-content').length && !$('.main-content-inner').hasClass('m-edit')) return;
					$('.viewport', this).mCustomScrollbar('scrollTo', (e.deltaY > 0? '+=300' : '-=300'), {
						scrollEasing: 'linear',
						scrollInertia: 120, 
						timeout: 0
					});
				});
			});
			setHeight();
		})();
		
		// высота белых списков
		(function() {
			var setHeight = function() {
				$('.white-block-row').each(function() {
					if ($(this).is('.a-hidden') || $(this).closest('.a-hidden').length) return;
					var maxH = 0;
					var cells = $('>*', this);
					if (cells.length < 2) return;
					cells.height('auto').each(function() {
						if ($(this).height() > maxH) {
							maxH = $(this).height();
						}
					}).height(maxH);
				});
			};
			$(window).on('resizeWhiteBlocks resize', setHeight);
			$('BODY').on('ui-sortable-sorted', '.sortable', setHeight);
			$('BODY').on('ui-tabs-beforeChange', '.tabs-cont.main-tabs', function() {
				var speed = $(this).data('speed');
				if (typeof speed === 'undefined') {
					speed = 350;
				} else {
					speed += 30;
				}
				setTimeout(function() {
					$(window).trigger('scroll').resize();
					setHeight();
					setTimeout(function() {
						$(window).trigger('scroll').resize();
					}, 400);
				}, speed);
			});
			setHeight();
		})();
		
		// смена url при переключении табов
		$('BODY').on('ui-tabs-beforeChange', '.tabs-cont.main-tabs', function(e, page) {
			if (editContent.animation()) return;
			var tabs = $(this);
			filter($(page, tabs));
			setTimeout(function() {
				var data = tabs.closest('.edit-content').length? {id: tabs.closest('.edit-content').attr('id')} : {};
				var tab = $('.content-options', tabs).find('.tab-title.m-current, OPTION:selected');
				tab = tab.data('url') || tab.attr('href') || '';
				history.replaceState(data, '', tab);
			}, 20);
		});
		
		// изменение размеров textarea
		$('TEXTAREA.resizeable').each(function() {
			var txt = $(this);
			var wrap = $('<div />', {class: 'txt-wrap'});
			var inner = $('<div />', {class: 'txt-inner'});
			var minHeight = txt.data('min-height') || 0;
			var maxHeight = txt.data('max-height') || Number.POSITIVE_INFINITY;
			var checkSize = function(enter) {
				inner.text(txt.val() + (enter? '\n\n' : '\n'));
				var txtHeight = txt.height();
				var innerHeight = inner.height();
				if (!txtHeight || !innerHeight) return;
				if (innerHeight < minHeight) innerHeight = minHeight;
				else if (innerHeight > maxHeight) innerHeight = maxHeight;
				else innerHeight += 2;
				txt.height(innerHeight);
			};
			wrap.css({
				width: txt.css('width'),
				position: 'relative'
			});
			inner.css({
				boxSizing: txt.css('box-sizing'),
				padding: txt.css('padding'),
				border: txt.css('border'),
				font: txt.css('font'),
				whiteSpace: 'pre-wrap',
				position: 'absolute',
				width: '100%',
				zIndex: 1,
				left: 0,
				top: 0
			});
			txt.css({
				position: 'relative',
				overflow: 'hidden',
				zIndex: 2
			});
			txt.wrap(wrap);
			txt.after(inner);
			checkSize();
			txt.bind('focus blur change keyup mouseup', function() {
				checkSize();
			}).bind('keydown', function(e) {
				if (e.keyCode === 13) checkSize(true);
			});
		});
		
	});
});
