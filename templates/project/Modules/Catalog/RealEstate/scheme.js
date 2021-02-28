$(function() {
	require(['ui', 'poly', 'floatBlock'], function(ui, Poly, floatBlock) {
		
		var scheme = $('.scheme-block');
		var back = $('.top-bg .back');
		var title = $('.top-bg .scheme-title');
		
		var statusColors = {
			for_sale: '#ff7e00',
			sale: '#ff7e00',
			sold: '#000',
			soon: '#fff',
			not: '#fff'
		};
		var animated = false;
		var resizeTimer = 0;
		var addPolys = function() {};
		var removePolys = function() {};
		
		var changeContent = function(newHtml, callback) {
			if (animated) return false;
			animated = true;
			callback = callback || function() {};
			var oldImgH = $('.scheme-img IMG').height();
			var oldContH = scheme.height();
			scheme.height(oldContH);
			TweenMax.to($('.scheme-inner', scheme), 0.4, {
				opacity: 0,
				onComplete: function() {
					var content = $('<DIV />').html(newHtml);
					$('.scheme-inner', content).css({opacity: 0});
					scheme.html(content.html());
					if ($('.back-btn-data', scheme).length) {
						back.attr('title', $('.back-btn-data', scheme).data('title')).removeClass('a-hidden');
					} else {
						back.addClass('a-hidden');
					}
					$(window).resize();
					var changeTimer = 0;
					var inner = $('.scheme-inner', scheme);
					var newImg = $('.scheme-img IMG', scheme);
					var newImgH = newImg.height();
					var change = function() {
						clearInterval(changeTimer);
						setTimeout(function() {
							newImgH = newImg.height();
							TweenMax.to(scheme, 0.4, {
								height: newImg.length? oldContH + (newImgH - oldImgH) : 130,
								onComplete: function() {
									scheme.css({height: 'auto'});
									animated = false;
									callback();
								}
							});
							TweenMax.to(inner, 0.4, {opacity: 1});
						}, 300);
					};
					$('.poly-cont', scheme).replaceWith($('.poly-cont IMG', scheme));
					$('.default-scheme', scheme).css({display: 'block', opacity: 1});
					$('.scheme-item', scheme).css({display: 'none', opacity: 0});
					title.text(inner.data('title'));
					initScheme();
					if (!newImg.length || newImgH) {
						change();
					} else {
						changeTimer = setInterval(function() {
							newImgH = newImg.height();
							if (newImgH) change();
						}, 200);
					}
				}
			});
		};
		
		var initScheme = function() {
			if ($('.poly-cont', scheme).length) return false;
			$('.scheme-img IMG').css({maxHeight: ($(window).height() - 120)});
			new Poly($('.scheme-img IMG'), {}, function() {
				var poly = this;
				var header = $('.scheme-header');
				addPolys = function() {
					$('.scheme-item').each(function() {
						var p = $(this);
						var type = p.data('type');
						var coords = p.data('coords');
						if (!coords) return;
						else coords = coords.split('|');
						var disabled = p.data('disabled');
						var stat = p.data('status');
						var id = p.data('id');
						var parts = [];
						if (disabled) stat = 'not';
						if (stat !== 'sale' && stat !== 'for_sale') disabled = true;
						for (var i in coords) {
							poly.add({
								fill: statusColors[stat] || statusColors.not,
								cursor: disabled? 'default' : 'pointer',
								opacity: type === 'flat'? 0.3 : 0,
								coords: coords[i],
								'stroke-width': 0,
								ready: function() {
									parts.push(this);
								},
								mouseover: function() {
									var next = $('.item-' + id, header);
									var current = next.siblings();
									current.css({opacity: 1});
									next.css({display: 'none', opacity: 0});
									TweenMax.to(current, 0.2, {
										opacity: 0,
										onComplete: function() {
											current.css({display: 'none'});
											next.css({display: 'block', opacity: 0});
											TweenMax.to(next, 0.2, {opacity: 1});
										}
									});
									for (var i in parts) {
										parts[i].animate({opacity: 0.5}, 200);
									}
								},
								mouseout: function() {
									var next = $('.default-scheme', header);
									var current = $('.scheme-item', header);
									current.css({opacity: 1});
									next.css({display: 'none', opacity: 0});
									TweenMax.to(current, 0.2, {
										opacity: 0,
										onComplete: function() {
											current.css({display: 'none'});
											next.css({display: 'block', opacity: 0});
											TweenMax.to(next, 0.2, {opacity: 1, delay: 0.3});
										}
									});
									for (var i in parts) {
										parts[i].animate({opacity: (type === 'flat'? 0.3 : 0)}, 200);
									}
								},
								click: function() {
									if (disabled || animated) return false;
									if (!p.data('title')) {
										location.href = p.data('url');
									} else {
										$.post(p.data('url'), {id: id}, function(res) {
											if (res.content) {
												changeContent(res.content);
												if (window.history && res.data.url) {
													history.pushState({content: res.content}, '', res.data.url);
												}
											}
										}, 'json').error(function() {});
									}
									return false;
								}
							});
						}
					});
				};
				removePolys = function() {
					poly.remove();
				};
				addPolys();
			});
		};
		initScheme();
		
		// ссылка назад
		back.click(function() {
			if (animated) return false;
			var btn = $('.back-btn-data', scheme);
			if (btn.length) {
				$.post(btn.data('url'), {id: btn.data('id')}, function(res) {
					if (res.content) {
						changeContent(res.content);
						if (window.history && res.data.url) {
							history.pushState({content: res.content}, '', res.data.url);
						}
					}
				}, 'json').error(function() {});
			}
			return false;
		});
		if ($('.back-btn-data', scheme).length) {
			back.attr('title', $('.back-btn-data', scheme).data('title')).removeClass('a-hidden');
		} else {
			back.addClass('a-hidden');
		}
		
		// смена этажа/корпуса
		scheme.on('click', '.back-scheme, .arrow', function() {
			if (animated || $(this).hasClass('m-disabled')) return false;
			$.post($(this).data('url'), {id: $(this).data('id')}, function(res) {
				if (res.content) {
					changeContent(res.content);
					if (window.history && res.data.url) {
						history.pushState({content: res.content}, '', res.data.url);
					}
				}
			}, 'json').error(function() {});
			return false;
		});
		
		// кнопка назад в браузере
		if (window.history) {
			history.replaceState({content: scheme.html()}, '');
			window.addEventListener('popstate', function(e) {
				if (e.state.content) {
					changeContent(e.state.content);
				}
			}, false);
		}
		
		// плавающая шапка
		floatBlock('.scheme-header', {
			cont: scheme,
			underTop: function() {
				$(this).removeClass('m-fixed m-bottom');
			},
			overTop: function() {
				$(this).addClass('m-fixed').removeClass('m-bottom');
			},
			overMax: function() {
				$(this).removeClass('m-fixed').addClass('m-bottom');
			}
		});
		
		$(window).resize(function() {
			removePolys();
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(addPolys, 100);
		});
		
	});
});