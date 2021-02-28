$(function() {
	require(['ui', 'scrollEvent'], function(ui, scrollEvent) {
		
		var filter = $('.filter');
		var result = $('.filter-result');
		var filterStr = filter.formSerialize();
		var submitFilter = function(opt) {
			if (!opt && filter.formSerialize() === filterStr) return;
			filterStr = filter.formSerialize();
			var data = {ajax: 1};
			if (opt) data = _.extend(data, opt);
			ui.form.submit(filter, {
				data: data,
				ignoreempty: 1,
				url: window.location.pathname,
				success: function(res) {
					if ('history' in window) {
						history.replaceState({}, '', '?' + filter.formSerialize());
					}
					var changeInner = false;
					var content = $('<DIV />').html(res.content);
					var fullContent = content;
					var toChange = result;
					if (opt && opt.page) {
						var newItems = $('.item-wrap', content).css({opacity: 0});
						var seeMore = $('.more-row', fullContent);
						toChange.append(newItems);
						newItems.each(function(i) {
							var item = $(this);
							setTimeout(function() {
								TweenMax.to(item, 0.4, {opacity: 1});
							}, i*200);
						});
						$('.more-row', result).appendTo(toChange).replaceWith(seeMore);
					} else {
						TweenMax.to(toChange, 0.4, {
							opacity: 0,
							onComplete: function() {
								if (changeInner) {
									toChange.replaceWith(content.css({opacity: 0}));
									toChange = content;
								} else {
									toChange.html(res.content);
								}
								TweenMax.to(toChange, 0.4, {opacity: 1});
							}
						});
					}
				}
			});
		};
		filter.on('change', 'INPUT[type="checkbox"], INPUT[type="radio"]', function() {
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
		if (!$('.order-radio:checked', filter).length) {
			$('.order-radio[data-default]', filter).prop('checked', true).change();
		}
		if (!$('.view-radio:checked', filter).length) {
			$('.view-radio[data-default]', filter).prop('checked', true).change();
		}
		$('INPUT[type="checkbox"], INPUT[type="radio"]', filter).change();
		filter.on('submit', function() {
			submitFilter();
			return false;
		});
		filter.on('change', 'INPUT:not(.range-input), SELECT, TEXTAREA', function() {
			submitFilter();
		});
		filter.on('slidechange', '.slider', function() {
			submitFilter();
		});
		result.on('click', '.see-more', function() {
			var page = $(this).data('page');
			if (!page) return false;
			submitFilter({page: page});
			return false;
		});
		
		//fixed main-menu
		if ( $('.main-menu').length ) {
			scrollEvent({
				'.page-header': {
					start: 0,
					outActive: function() {
						$('.main-menu').addClass('m-fixed');
						console.log('bom bom')
					},
					onActive: function() {
						$('.main-menu').removeClass('m-fixed');
					}
				}
			});		
		}
		
	});
});