define(['ui', 'message'], function(ui, message) {

	var filterScripts = function(page) {
		var form = $('.page-aside .items-filter');
		if (form.data('init-filter')) return false;
		else form.data('init-filter', true);
		var itemsList = $('.items-list', page);
		var asideFloat = $('.float-button', form);
		var floatShown = false;
		var hideFloat = function() {
			if (!asideFloat.length) return;
			asideFloat.stop(true, true).fadeOut(300);
			floatShown = false;
		};
		var sending = false;
		ui.form(form, {
			method: 'GET',
			afterSubmit: function() {
				if (form.data('silent-submit')) form.data('silent-submit', false);
				else {
					$('INPUT, SELECT, TEXTAREA', form).not('[data-disabled]').each(function() {
						if ($(this).val() === '') $(this).attr('disabled', true);
					});
					history.replaceState({}, '', '?' + form.formSerialize());
				}
			},
			success: function(res) {
				itemsList.html(res.content);
				$(window).resize();
				sending = false;
				ui.initAll();
				hideFloat();
			},
			errors: function(error) {
				message.errors({error: error});
				sending = false;
				hideFloat();
			},
			serverError: function(err) {
				message.serverErrors(err);
				sending = false;
				hideFloat();
			}
		});
		if (asideFloat.length) {
			var floatShow = 4000;
			var floatHideTimer = 0;
			var floatOldTop = 0;
			var asideFloatTrigger = function(e) {
				if (sending) return false;
				var top = e.target.offsetTop;
				ui.form.submit(form, {
					method: 'GET',
					ignoreempty: true,
					data: {only_count: 1},
					success: function(num) {
						$('.num', asideFloat).text(num);
						asideFloat.stop(true, true);
						if (floatShown) {
							var pathTime = Math.abs(floatOldTop - top)/0.6;
							if (pathTime > 500) pathTime = 500;
							asideFloat.animate({top: top, opacity: 1}, pathTime);
						} else {
							asideFloat.css({top: top}).fadeIn(300);
						}
						floatOldTop = top;
						floatShown = true;
						clearTimeout(floatHideTimer);
						floatHideTimer = setTimeout(function() {
							hideFloat();
						}, floatShow);
					}
				});
			};
			form.on('change', 'INPUT:not(.slider-input), TEXTAREA, SELECT', asideFloatTrigger);
			form.on('slidestop', '.range', asideFloatTrigger);
			asideFloat.hover(function() {
				if (sending) return false;
				clearTimeout(floatHideTimer);
			}, function() {
				if (sending) return false;
				clearTimeout(floatHideTimer);
				floatHideTimer = setTimeout(function() {
					hideFloat();
				}, floatShow);
			});
			$('.clear-form, .submit', form).mousedown(function() {
				if (sending) return false;
				sending = true;
				hideFloat();
			});
		}
	};
	
	var initFilters = function(page) {
		if (page && page.length) {
			var currentPage = page;
		} else {
			var currentPage = $('.edit-content.m-edit-open:last');
			if (!currentPage.length) currentPage = $('.view-content');
			currentPage = currentPage.find('.tab-page.m-current');
		}
		
		if (currentPage.find('.hidden-filter').length) {
			var currentFilter = $('.page-aside .aside-filter');
			var filter = currentPage.find('.hidden-filter');
			if (currentFilter.data('tab-page') && currentFilter.data('tab-page').is(currentPage) && filter.data('filter-init')) return;
			else filter.data('filter-init', true);
			$('INPUT', filter).each(function() {
				if ($(this).val()) {
					$(this).attr('value', $(this).val());
				}
			});
			filter = $(filter.html()).data('tab-page', currentPage).hide();
			if (currentFilter.data('tab-page') && currentFilter.data('tab-page').is(currentPage)) {
				$('.page-aside .aside-filter').replaceWith(filter);
				if ($('.page-aside .aside-filter .field').length) {
					$('.page-aside .aside-filter').show();
				}
				ui.initAll();
				$(window).resize();
				filterScripts(page);
			} else {
				$('.page-aside .aside-filter').fadeOut(300, function() {
					$(this).replaceWith(filter);
					ui.initAll();
					$(window).resize();
					filterScripts(page);
					if ($('.page-aside .aside-filter .field').length) {
						$('.page-aside .aside-filter').fadeIn(600);
					}
				});
			}
		} else if ($('.items-list').length) {
			$('.page-aside .aside-filter').fadeOut(300, function() {
				$(this).replaceWith('<section class="aside-filter a-hidden"></section>');
			});
		}
	};
	
	initFilters.replace = function(html, page) {
		if (page && page.length) {
			var currentPage = page;
		} else {
			var currentPage = $('.edit-content.m-edit-open');
			if (!currentPage.length) currentPage = $('.view-content');
			currentPage = currentPage.find('.tab-page.m-current');
		}
		html = $('<div />').html(html);
		if (html.find('.hidden-filter').length) {
			currentPage.find('.hidden-filter').remove();
			currentPage.find('FORM.items-edit').after(html.find('.hidden-filter'));
		}
		return html.html();
	};
	
	initFilters.submitHiddenFilter = function(page) {
		if (!page || !page.length) return;
		var filter = page.find('.hidden-filter');
		$('.page-aside .aside-filter').addClass('a-hidden').replaceWith(filter.html());
		filterScripts(page);
		$('.page-aside .items-filter').submit();
	};
	
	initFilters();
	return initFilters;
});