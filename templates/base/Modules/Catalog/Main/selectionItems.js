$(function() {
	require(['tap', 'catalog-item', 'subcatalog'], function(tap, initCatalogItem, initSubcatalog) {
		var oldBrowser = !('pushState' in history);
		var startHtml = $('.items-list-cont').html();
		var filterForm = $('.aside-filter FORM');
		var clearButtonCont = $('.clear-button');
		var isAnimated = false;
		var firstLoad = true;
		
		// смена контента
		var changeContent = function(content) {
			if (location.search) {
				clearButtonCont.slideDown();
			} else {
				clearButtonCont.slideUp();
			}
			content = $('<div>').html(content);
			$('.catalog-list > LI, .empty-result', content).css({opacity: 0});
			$('HTML, BODY').stop(true, true).animate({
				scrollTop: $('.main-content').offset().top - 36
			}, 400);
			$('.catalog-list, .empty-result').animate({opacity: 0}, 500, function() {
				$('.items-list-cont').empty().append(content);
				if ($('.catalog-list > LI', content).length) {
					initCatalogItem();
					initSubcatalog();
					initSwitchers();
					isAnimated = false;
					$('.edited-text.type-descr').fadeIn(200);
					$('.catalog-list > LI', content).each(function(i) {
						var item = $(this);
						setTimeout(function() {
							item.animate({opacity: 1}, 300);
						}, i*200);
					});
				} else {
					$('.edited-text.type-descr').fadeOut(200);
					$('.empty-result').animate({opacity: 1}, 300);
					isAnimated = false;
				}
                if ($('.page_title_from_ajax' ).text()!= ''){
                    document.title=$('.page_title_from_ajax' ).text();
                }
			});
		}
		
		// меняем контент при перемещении "вперёд", "назад""
		window.onpopstate = function(event) {
			if (firstLoad) {
				firstLoad = false;
			} else {
				changeContent(event.state? event.state.html : startHtml);
			}			
		};
		
		// меняем контент при сабмите формы
		filterForm.bind('submit', function(e) {
			if (isAnimated) return false;
			isAnimated = true;
			hideFloatButton();
			// не отправляем пустые поля
			$('INPUT:text, INPUT:hidden, TEXTAREA, SELECT', filterForm).each(function() {
				if ($(this).is('SELECT')) {
					if (!$('OPTION:selected', this).val() && $('OPTION:selected', this).val() !== 0) {
						$(this).attr('disabled', 'disabled');
					}
				} else {
					if (!$(this).val() && $(this).val() !== 0) {
						$(this).attr('disabled', 'disabled');
					}
				}
			});
			$('.slider-cont').each(function() {
				var cont = $(this);
				if ($('.input-from', cont).val() == $('.double-slider', cont).data('min')) $('.input-from', cont).attr('disabled', 'disabled');
				if ($('.input-to', cont).val() == $('.double-slider', cont).data('max')) $('.input-to', cont).attr('disabled', 'disabled');
			});
			// ajax-овый фильтр
			if (!oldBrowser) {
				e.preventDefault();
				var query = filterForm.attr('action') + '?' + filterForm.serialize();
				if (query == filterForm.attr('action') + '?') query = filterForm.attr('action');
				filterForm.ajaxSubmit({
					data: {ajax: 1},
					success: function(res) {
						$('INPUT:text, INPUT:hidden, TEXTAREA, SELECT', filterForm).removeAttr('disabled');
						$('.chosen', filterForm).trigger('liszt:updated');
						$('.chzn-single SPAN', filterForm).each(function() {							
							$(this).html($('.active-result.result-selected', $(this).closest('.chzn-container')).html());
						});
						history.pushState({html: res}, 'filtering', query);
						changeContent(res);

					}
				});
			}
		});
		
		// очистить фильтр
		$('.clear', clearButtonCont).bind('click', function() {
			$('.sort-input', filterForm).val('');
			$('.page-input', filterForm).val('');
			filterForm.clearForm().submit();
			$('.b-cbx, .b-radio', filterForm).removeClass('m-checked');
			$('.chosen OPTION:first', filterForm).attr('selected', 'selected');
			$('.chosen', filterForm).trigger('liszt:updated');
			$('.double-slider', filterForm).each(function() {
				$('.ui-slider-handle:last', this).css({left: '100%'});
				$('.ui-slider-handle:first', this).css({left: 0});
				$('.ui-slider-range', this).css({
					width: '100%',
					left: 0
				});
			});
		});
		
		function initSwitchers() {
			// сортировка
			if (!oldBrowser) {
				$('.sort-menu .sort-list LI A').bind('click', function(e) {
					e.preventDefault();
					$('.sort-menu .sort-title').text($(this).text());
					$('.sort-input', filterForm).attr('name', $(this).data('sort')).val($(this).data('val'));
					$('.page-input', filterForm).val('');
					filterForm.submit();
				});
			}
			if (('ontouchstart' in window) || window.navigator.msMaxTouchPoints) {
				$('.sort-menu').removeClass('m-hoverable');
				$('.sort-menu').bind('touchstart MSPointerDown', function(e) {
					if ($('.sort-menu').hasClass('m-hover')) {
						if ($(e.target).closest('.sort-title').length || $(e.target).is('.sort-title')) {
							$('.sort-menu').removeClass('m-hover');
						}
					} else {
						$('.sort-menu').addClass('m-hover');
					}
				});
				tap($(document), $('.sort-menu'), function() {
					$('.sort-menu').removeClass('m-hover');
				});
			}

			// смена страниц
			if (!oldBrowser) {
				$('.paging .pages LI A').live('click', function(e) {
					e.preventDefault();
					if ($(this).parent().hasClass('m-current')) return;
					$('.page-input', filterForm).val($(this).text());
					filterForm.submit();
				});
				$('.paging .prev').bind('click', function(e) {
					e.preventDefault();
					var num = parseInt($('.paging .pages LI.m-current A').text()) - 1;
					$('.page-input', filterForm).val(num);
					filterForm.submit();
				});
				$('.paging .next').bind('click', function(e) {
					e.preventDefault();
					var num = parseInt($('.paging .pages LI.m-current A').text()) + 1;
					$('.page-input', filterForm).val(num);
					filterForm.submit();
				});
			}
		}	
		initSwitchers();
		
		// плавающтй блок
		var floatSpeed = 400;
		var floatLifeTime = 3000;
		var floatHidingTime = 400;
		var floatVisible = false;		
		var floatLifeTimer = 0;
		var filterFormTop = filterForm.offset().top;
		var floatButton = $('.float-block', filterForm);
		function showFloatButton() {
			if (!floatVisible) floatButton.css({opacity: 0});
			floatVisible = true;
			floatButton.show().animate({opacity: 1}, floatHidingTime);
		}
		function hideFloatButton() {
			floatButton.animate({opacity: 0}, floatHidingTime, function() {
				floatVisible = false;
				floatButton.hide();
			});
		}
		var floatMoving = function(item, dif) {
			clearTimeout(floatLifeTimer);
			dif = (!dif && dif !== 0)? 15 : dif;
			var top = item.offset().top - filterFormTop - dif;
			if (floatVisible) {
				floatButton.stop().animate({top: top}, floatSpeed);
			} else {
				floatButton.css({top: top});
			}
			showFloatButton();
			floatLifeTimer = setTimeout(hideFloatButton, floatLifeTime);
		}
		$('INPUT', filterForm).bind('change', function() {
			if (!$(this).closest('.slider-cont').length) {
				floatMoving($(this));
			} else {
				floatMoving($(this).closest('.slider-cont'));
			}			
		});
		$('.double-slider', filterForm).bind('slidechange', function() {
			floatMoving($(this).closest('.slider-cont'));
		});
		$('SELECT', filterForm).bind('change', function() {
			floatMoving($(this).closest('.filter-items'), 5);
		});
		
		
		// пустой поиск
		var bottomLine = $('.bottom-float-line');
		$(window).bind('scroll resize touchmove MSPointerMove', function() {
			if (!$('.empty-search').length) return;
			var empty = $('.empty-search-inner');
			$('.empty-search').height($('.page-aside').height() - 39);
			var searchHeight = empty.height();
			var top = $(window).scrollTop() + searchHeight + 36;
			var bottom = bottomLine.offset().top;
			if ($(window).scrollTop() < 271) {
				empty.removeClass('m-fixed m-abs');
			} else if (top >= bottom) {
				empty.removeClass('m-fixed').addClass('m-abs');
			} else {
				empty.addClass('m-fixed').removeClass('m-abs');
			}
		});
		
	});
});