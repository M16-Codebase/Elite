/*
 * TODO: 
 * triggers - el.trigger(triggerStr + opt.component + '-action', [opt]);
 * onInit
 */

define(function() {
	var idIterator = 0;
	var initedComponents = {};
	var generalComponents = {};
	var oldIe = $('BODY').hasClass('browser-oldie');
	var ignoreClass = '.ui-ignore'; 
	var initStr = '-init-ui';
	var triggerStr = 'ui-';

	// Вызов события при клике за пределами указанного элемента
	var clickOut = function(el, callback) {
		if (!el) return false;
		else el = $(el);
		if (!el.length) return false;
		callback = callback || function() {};
		$('BODY').on('click touchstart', function(e) {
			var target = $(e.target);
			if (!target.is(el) && !target.closest(el).length) {
				var result = callback.call(el, e);
				if (result === false) return false;
			}
		});
	};

	var Component = function(name, defaultEl, defaultOptions, methods) {
		if (!name) return false;
		var comp = this;
		comp.name = name;
		defaultOptions = defaultOptions || {};
		methods = methods || {};
		methods.init = methods.init || function() {};
		for (var event in methods) {
			(function() {
				var e = event;
				comp[e] = function(el, userOptions) {
					var args = arguments || [];
					userOptions = userOptions || ((typeof el === 'object' && !el.jquery)? el : {});
					if (el && (typeof el === 'string' || el.jquery)) {
						el = $(el);
					} else {
						el = $(defaultEl || null);
						if (generalComponents[name] && generalComponents[name].el && generalComponents[name].el.length) el = el.add(generalComponents[name].el);
					}
					if (!el.length) return false;
					el.each(function() {
						var id = 0;
						var options = {};
						var savedOptions = {};
						var newArguments = [];
						var elUserOptions = $.extend({}, userOptions);
						if ($(this).data(name + initStr)) {
							id = $(this).data(name + initStr);
							savedOptions = initedComponents[id] || {};
						} else {
							id = ++idIterator;
							if (!generalComponents[name].el) generalComponents[name].el = $(this);
							else generalComponents[name].el = generalComponents[name].el.add($(this));
						}
						for (var i in defaultOptions) {
							var tdo = typeof defaultOptions[i];
							if (elUserOptions[i] !== undefined) options[i] = elUserOptions[i];
							else if (savedOptions[i] !== undefined) options[i] = savedOptions[i];
							else {
								if ($(this).data(i) !== undefined && (tdo === 'string' || tdo === 'number' || tdo === 'boolean')) {
									options[i] = $(this).data(i);
									elUserOptions[i] = options[i];
								} else {
									options[i] = defaultOptions[i];
								}
							}
						}
						elUserOptions.userOptions = elUserOptions.userOptions || {};
						options.userOptions = $.extend(elUserOptions.userOptions, elUserOptions);
						options.defaultOptions = defaultOptions;
						options.component = name;
						initedComponents[id] = options;
						for (var a in args) {
							newArguments.push(args[a]);
						}
						newArguments[0] = $(this);
						newArguments[1] = options;
						methods[e].apply(comp, newArguments);
						if (!$(this).data(name + initStr)) {
							$(this).data(name + initStr, id);
						}
					});
				};
			})();
		}
		generalComponents[name] = comp;
	};



	/******************** COMPONENRTS ********************/

	// SCROLLER
	new Component('scroller', '.scroller', {
		target: '',
		shift: 0,
		speed: 400,
		except: '.scroll-except',
		beforeScroll: function() {},
		afterScroll: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (opt.target === undefined || opt.target === '') {
				if (el.attr('href') !== undefined && el.attr('href') !== '') opt.target = el.attr('href');
				else opt.target = '';
			}
			if (opt.target === '' || opt.target === '#') return false;
			if (isNaN(opt.target) && !$(opt.target).length) return false;
			el.off('click').on('click', function(e) {
				if ($(e.target).is(opt.except) || $(e.target).closest(opt.except).length) return true;
				var target = opt.target;
				var beforeScroll = opt.beforeScroll.call(el);
				if (beforeScroll === false) return false;
				else if (beforeScroll !== undefined) target = beforeScroll;
				var scroll = isNaN(target)? ($(target).offset().top + opt.shift) : parseInt(target);
				$('HTML, BODY').stop(true, true).animate({
					scrollTop: scroll
				}, opt.speed, function(){
					if (target.search('#') !== -1) {
						window.location.hash = target;
					};
					opt.afterScroll;
				});
				return false;
			});
		},
		scroll: function(el, opt) {
			if (opt.target === '') return false;
			if (isNaN(opt.target) && !$(opt.target).length) return false;
			var target = opt.target;
			var beforeScroll = opt.beforeScroll.call(el);
			if (beforeScroll === false) return false;
			else if (beforeScroll !== undefined) target = beforeScroll;
			var scroll = isNaN(target)? ($(target).offset().top + opt.shift) : parseInt(target);
			el.stop(true, true).animate({
				scrollTop: scroll
			}, opt.speed, opt.afterScroll);
		}
	});


	// LINK WRAP
	new Component('linkwrap', '.link-wrap', {
		target: '.link-target',
		except: '.link-except',
		targetblank: false
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			var link = $(opt.target, el).length? $(opt.target, el).first() : el;
			var url = link.data('url') || link.attr('href') || null;
			if (!link.is(el)) {
				link.on('click', function() {
					el.mousedown();
					return false;
				});
			}
			el.off('mousedown').on('mousedown', function(e) {
				if (!url) return this;
				var target = $(e.target);
				var key = e.button;
				var newTab = false;
				if (oldIe) key = (key === 1)? 0 : ((key === 4)? 1 : 2);
				if (key === 2) return this;
				if (target.is(opt.except) || target.closest(opt.except).length) return this;
				if (key === 1 || e.ctrlKey || opt.targetblank || 
					(opt.userOptions.targetblank === undefined && link.attr('target') === '_blank')) newTab = true;
				$(this).data('linkwrap-url', url);
				$(this).data('linkwrap-newtab', newTab);
				return false;
			}).off('mouseup').on('mouseup', function() {
				if (!$(this).data('linkwrap-url')) return this;
				var url = $(this).data('linkwrap-url');
				var newTab =  $(this).data('linkwrap-newtab');
				if (newTab) {
					var newWindow = window.open(url);
					newWindow.focus();
				} else window.location = url;
				$(this).data('linkwrap-url', false);
				return false;
			}).off('mouseleave').on('mouseleave', function() {
				$(this).data('linkwrap-url', false);
			});
		}
	});


	// POPUP
	new Component('popup', '.popup-window', {
		title: 'Сообщение',
		width: 600,
		class: '',
		closebtn: '.close-popup',
		dialogOptions: {},
		open: function() {},
		close: function() {},
		beforeOpen: function() {},
		beforeClose: function() {},
		alwaysOnOpen: function() {},
		alwaysOnClose: function() {},
		alwaysBeforeClose: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.dialog) return;
			el.dialog($.extend({
				modal: true,
				autoOpen: false,
				resizable: false,
				dialogClass: opt.class,
				title: opt.title,
				width: opt.width,
				open: opt.open,
				close: opt.close,
				beforeClose: opt.beforeClose
			}, opt.dialogOptions));
			el.on('click', opt.closebtn, function() {
				el.dialog('close');
				return false;
			}).on('dialogopen', function() {
				$('.ui-widget-overlay').click(function() {
					el.dialog('close');
				});
				if (opt.userOptions.alwaysOnOpen) {
					opt.userOptions.alwaysOnOpen.apply(this, arguments);
				}
			});
			if (opt.userOptions.alwaysOnClose) {
				el.on('dialogclose', opt.userOptions.alwaysOnClose);
			}
			if (opt.userOptions.alwaysBeforeClose) {
				el.on('dialogbeforeclose',opt.userOptions.alwaysBeforeClose);
			}
		},
		open: function(el, opt) {
			if (!el.dialog) return;
			if (opt.userOptions) this.init(el, opt.userOptions);
			var beforeOpen = opt.beforeOpen.apply(el);
			if (beforeOpen === false) return false;
			el.dialog('open');
		},
		close: function(el) {
			if (!el.dialog) return;
			el.dialog('close');
		}
	});


						// POPUP ALERTS
						new Component('popupAlert', '.popup-alert', {
							okClass: 'popup-alert-ok',
							errorClass: 'popup-alert-error',
							confirmClass: 'popup-alert-confirm',
							title: '',
							text: '',
							width: 800,
							oktext: '',
							cancelText: '',
							errors: [],
							errorText: ''
						}, {
							init: function(el, opt) {
								if (el.closest(ignoreClass).length) return;
								if (!el.filter(opt.okClass).length) {
									
								}
								if (!el.filter(opt.errorClass).length) {
									
								}
								if (!el.filter(opt.confirmClass).length) {
									
								}
							},
							ok: function() {},
							error: function() {},
							confirm: function() {}
						});


	// DROPDOWN
	new Component('dropdown', '.dropdown', {
		toggle: '.dropdown-toggle',
		list: '.dropdown-menu',
		items: '>*',
		inactiveclass: 'm-inactive',
		openclass: 'm-open',
		hoverable: true,
		speed: 200,
		open: function() {},
		close: function() {},
		select: function() {},
		beforeOpen: function() {},
		beforeClose: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			var dropdown = this;
			var openTimer, closeTimer;
			var toggle = el.find(opt.toggle);
			var list = el.find(opt.list);
			var items = list.find(opt.items);
			var open = el.hasClass(opt.openclass);
			if (open) list.show();
			else list.hide();
			if (opt.hoverable) {
				el.off('mouseenter').on('mouseenter', function() {
					clearTimeout(closeTimer);
					openTimer = setTimeout(function() {
						dropdown.open(el, opt);
					}, 50);
				}).off('mouseleave').on('mouseleave', function() {
					clearTimeout(openTimer);
					closeTimer = setTimeout(function() {
						dropdown.close(el, opt);
					}, 200);
				});
			} else {
				el.off('mouseenter mouseleave');
			}
			toggle.off('click').on('click', function(e) {
				if (list.is(':animated')) {
					list.stop(true, true);
				} else {
					if (!opt.hoverable) e.preventDefault();
					if (el.hasClass(opt.openclass)) dropdown.close(el, opt);
					else dropdown.open(el, opt);
				}
			});
			items.off('click').on('click', function(e) {
				var res = opt.select.call(this, e);
				if (res !== false) dropdown.close(el, opt);
			});
			clickOut(el, function() {
				dropdown.close(el, opt);
			});
		},
		open: function(el, opt) {
			if (el.hasClass(opt.openclass)) return false;
			if (el.hasClass(opt.inactiveclass)) return false;
			var list = el.find(opt.list);
			var beforeOpen = opt.beforeOpen.apply(el);
			if (beforeOpen === false) return false;
			el.addClass(opt.openclass);
			list.stop(true, true).slideDown(opt.speed, opt.open);
		},
		close: function(el, opt) {
			if (!el.hasClass(opt.openclass)) return false;
			var list = el.find(opt.list);
			var beforeClose = opt.beforeClose.apply(el);
			if (beforeClose === false) return false;
			el.removeClass(opt.openclass);
			list.stop(true, true).slideUp(opt.speed, opt.close);
		}
	});


	// SLIDEBOX
	new Component('slidebox', '.slidebox', {
		cont: '.slidebox-cont',
		header: '.slide-header',
		body: '.slide-body',
		except: '.box-except',
		openclass: 'm-open',
		hiddenclass: 'a-hidden',
		closesiblings: true,
		speed: 200,
		open: function() {},
		close: function() {},
		beforeOpen: function() {},
		beforeClose: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			var slidebox = this;
			var header = el.find(opt.header);
			var body = el.find(opt.body);
			if (el.hasClass(opt.openclass)) {
				body.css({display: 'block'});
			} else {
				body.css({display: 'none'});
			}
			header.off('click').on('click', function(e) {
				if ($(e.target).is(opt.except) || $(e.target).closest(opt.except).length) return true;
				if (el.hasClass(opt.openclass)) {
					slidebox.close(el, opt);
				} else {
					slidebox.open(el, opt);
				}
				return false;
			});
		},
		open: function(el, opt) {
			if (el.hasClass(opt.openclass)) return false;
			var slidebox = this;
			var body = el.find(opt.body);
			var beforeOpen = opt.beforeOpen.apply(el);
			el.trigger(triggerStr + opt.component + '-beforeOpen');
			if (beforeOpen === false) return false;
			if (opt.closesiblings && el.closest(opt.cont).length) {
				var siblings = el.closest(opt.cont).find(slidebox.el);
				if (siblings.length) slidebox.close(siblings);
			}
			el.addClass(opt.openclass);
			body.stop(true, true).removeClass(opt.hiddenclass).slideDown(opt.speed, function() {
				$(this).css({display: 'block'});
				opt.open.apply(el);
				el.trigger(triggerStr + opt.component + '-open');
			});
		},
		close: function(el, opt) {
			if (!el.hasClass(opt.openclass)) return false;
			var body = el.find(opt.body);
			var beforeClose = opt.beforeClose.apply(el);
			el.trigger(triggerStr + opt.component + '-beforeClose');
			if (beforeClose === false) return false;
			el.removeClass(opt.openclass);
			body.stop(true, true).addClass(opt.hiddenclass).slideUp(opt.speed, function() {
				$(this).css({display: 'none'});
				opt.close.apply(el);
				el.trigger(triggerStr + opt.component + '-close');
			});
		}
	});


	// TABS
	new Component('tabs', '.tabs-cont', {
		titles: '.tab-title',
		pages: '.tab-page',
		inactiveclass: 'm-inactive',
		currentclass: 'm-current',
		hiddenclass: 'a-hidden',
		speed: 300,
		beforeChange: function() {},
		change: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			var titles = el.find(opt.titles);
			var pages = el.find(opt.pages);
			var pageTabs = {};
			var curPage = null;
			var oldPage = null;
			var firstTab = null;
			var switchTab = function(fast, callback) {
				if (!pageTabs[curPage]) return false;
				callback = callback || function() {};
				titles.removeClass(opt.currentclass);
				pageTabs[curPage].each(function() {
					if ($(this).is('SELECT')) {
						$(this).val(curPage).find('[data-target="' + curPage + '"]').prop('selected', true);
					} else {
						$(this).addClass(opt.currentclass);
					}
				});
				if (fast) {
					pages.addClass(opt.hiddenclass).removeClass(opt.currentclass).hide();
					pages.filter(curPage).removeClass(opt.hiddenclass).addClass(opt.currentclass).show();
					callback.call(el);
					el.trigger(triggerStr + opt.component + '-change');
				} else {
					pages.filter(oldPage).stop(true, true).css({display: 'block'}).fadeOut(opt.speed, function() {
						pages.addClass(opt.hiddenclass).removeClass(opt.currentclass);
						pages.filter(curPage).removeClass(opt.hiddenclass).addClass(opt.currentclass).css({display: 'none'}).fadeIn(opt.speed, function() {
							callback.call(el);
							el.trigger(triggerStr + opt.component + '-change');
						});
					});
				}
			};
			
			titles.each(function() {
				var selector = '';
				if ($(this).is('SELECT')) {
					$('OPTION', this).each(function() {
						selector = $(this).data('target') || $(this).attr('value') || '';
						if (selector) {
							if (!pageTabs[selector]) pageTabs[selector] = $(this).closest('SELECT');
							else pageTabs[selector] = pageTabs[selector].add($(this).closest('SELECT'));
							if (!firstTab) firstTab = selector;
						}
						if ($(this).is(':selected')) {
							curPage = selector;
						}
					});
				} else {
					selector = $(this).data('target') || $(this).attr('href') || '';
					if (selector) {
						if (!pageTabs[selector]) pageTabs[selector] = $(this);
						else pageTabs[selector] = pageTabs[selector].add($(this));
						if (!firstTab) firstTab = selector;
					}
					if ($(this).hasClass(opt.currentclass)) {
						curPage = selector;
					}
				}
			});
			if (!curPage && firstTab) curPage = firstTab;
			if (!curPage) return false;
			oldPage = curPage;
			switchTab(true);
				
			// селекты
			titles.filter('SELECT').off('change').on('change', function() {
				var newTab = $(this).find('OPTION:selected').length? $(this).find('OPTION:selected') : $(this).find('OPTION:first');
				var selector = newTab.data('target') || newTab.attr('value') || '';
				var beforeChange = opt.beforeChange.call(el, selector);
				el.trigger(triggerStr + opt.component + '-beforeChange', [selector]);
				if (typeof beforeChange === 'string') {
					selector = beforeChange;
				} 
				if (beforeChange !== false && selector && pages.filter(selector).length) {
					oldPage = curPage;
					curPage = selector;
				}
				switchTab(false, opt.change);
			});
				
			// кнопки
			titles.off('click').on('click', function() {
				if ($(this).hasClass(opt.inactiveclass)) return false;
				if ($(this).hasClass(opt.currentclass)) return false;
				var selector = $(this).data('target') || $(this).attr('href') || '';
				var beforeChange = opt.beforeChange.call(el, selector);
				el.trigger(triggerStr + opt.component + '-beforeChange', [selector]);
				if (beforeChange === false) {
					return false;
				} else if (typeof beforeChange === 'string') {
					selector = beforeChange;
				}
				if (!selector || !pages.filter(selector).length) return false;
				oldPage = curPage;
				curPage = selector;
				switchTab(false, opt.change);
				return false;
			});
		}
	});


	// CHECK
	new Component('check', '.cbx, .radio', {
		cbxclass: '',
		cbxwrapclass: 'cbx-wrap',
		radiowrapclass: 'radio-wrap',
		checkedclass: 'm-checked'
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.is('INPUT')) return;
			var name = el.attr('name');
			var cbx = el.is(':checkbox');
			if (!el.parent().is('.' + opt.cbxwrapclass + ', .' + opt.radiowrapclass)) {
				el.wrap('<div class="' + (cbx? opt.cbxwrapclass : opt.radiowrapclass) + (opt.cbxclass? (' ' + opt.cbxclass) : '') + '"></div>');
			}
			var cont = el.parent();
			if (el.is(':checked')) cont.addClass(opt.checkedclass);
			else cont.removeClass(opt.checkedclass);
			el.off('change').on('change', function() {
				if (el.is(':checked')) {
					cont.addClass(opt.checkedclass);
					if (!cbx) {
						$('INPUT.radio[name="' + name + '"]').parent().not(cont).removeClass(opt.checkedclass);
					}
				} else {
					cont.removeClass(opt.checkedclass);
				}
			});
			el.off(triggerStr + opt.component + '-update').on(triggerStr + opt.component + '-update', function() {
				if (el.is(':checked')) cont.addClass(opt.checkedclass);
				else cont.removeClass(opt.checkedclass);
			});
			cont.add(el.closest('LABEL')).off('click').on('click', function() {
				if (el.is(':radio') && el.is(':checked')) return false;
				el.prop('checked', !el.is(':checked')).trigger('change');
				return false;
			});
		}
	});


	// SELECT
	new Component('select', '.chosen', {
		noresults: 'Нет совпадений'
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.chosen) return;
			var custom = {};
			el.chosen({
				no_results_text: opt.noresults
			}).ready(function() {
				custom = el.next('.chzn-container');
				$('.chzn-single SPAN', custom).html($('.active-result.result-selected', custom).html());
			}).change(function() {
				$('.chzn-single SPAN', custom).html($('.active-result.result-selected', custom).html());
			});
		},
		update: function(el) {
			if (!el.chosen) return;
			el.trigger('liszt:updated');
		}
	});


	// MASK
	new Component('mask', '.mask, [data-mask]', {
		mask: '',
		maskOptions: {placeholder: ''}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.mask) return;
			var mask = opt.mask;
			if (!mask) mask = el.attr('mask') || el.data('mask') || '';
			if (!mask) return;
			el.mask(mask, opt.maskOptions);
		}
	});


	// SLIDER
	new Component('slider', '.slider', {
		value: '',
		min: 0,
		max: 100,
		step: 1,
		range: 0,
		rangeclass: 'range',
		cont: '.slider-wrap',
		inputmin: '.input-min',
		inputmax: '.input-max',
		textmin: '.text-min',
		textmax: '.text-max',
		singleinput: '.slide-input',
		singletext: '.slide-text',
		slideOptions: {},
		slide: function() {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.slider) return;
			var range = el.hasClass(opt.rangeclass)? true : opt.range;
			var cont = el.closest(opt.cont);
			var min = parseFloat(String(opt.min).replace(',', '.'));
			var max = parseFloat(String(opt.max).replace(',', '.'));
			var step = parseFloat(String(opt.step).replace(',', '.'));
			var inputMin = $(opt.inputmin, cont);
			var inputMax = $(opt.inputmax, cont);
			var textMin = $(opt.textmin, cont);
			var textMax = $(opt.textmax, cont);
			var singleInput = $(opt.singleinput, cont);
			var singleText = $(opt.singletext, cont);
			if (!inputMin.val()) inputMin.val(min);
			if (!inputMax.val()) inputMax.val(max);
			if (!singleInput.val()) singleInput.val(min);
			if (opt.userOptions.value !== undefined) {
				var value = opt.userOptions.value;
				if (typeof value !== 'object') {
					value = String(value).replace(',', '.').split(';');
				}
				if (value[0] !== undefined) inputMin.val(value[0]);
				if (value[1] !== undefined) inputMax.val(value[1]);
				if (value[0] !== undefined) singleInput.val(value[0]);
			}
			textMin.text(inputMin.val());
			textMax.text(inputMax.val());
			singleText.text(singleInput.val());
			el.slider($.extend({
				range: range? true : false,
				min: min,
				max: max,
				step: step,
				value: range? false : (singleInput.val() || min),
				values: range? [(inputMin.val() || min), (inputMax.val() || max)] : false,
				slide: function(event, ui) {
					if (range) {
						textMin.text(ui.values[0]);
						textMax.text(ui.values[1]);
						inputMin.val(ui.values[0]).change();
						inputMax.val(ui.values[1]).change();
					} else {
						singleText.text(ui.value);
						singleInput.val(ui.value).change();
					}
					opt.slide.apply(el, arguments);
				}
			}, opt.slideOptions));
			$('INPUT', cont).keyup(function() {
				if (range) {
					el.slider('values', [inputMin.val(), inputMax.val()]);
				} else {
					el.slider('value', $(this).val());
				}
			});
		},
		clear: function(el, opt) {
			if (!el.slider) return;
			var range = el.hasClass(opt.rangeclass)? true : opt.range;
			var cont = el.closest(opt.cont);
			var min = parseFloat(String(opt.min).replace(',', '.'));
			var max = parseFloat(String(opt.max).replace(',', '.'));
			var inputMin = $(opt.inputmin, cont);
			var inputMax = $(opt.inputmax, cont);
			var textMin = $(opt.textmin, cont);
			var textMax = $(opt.textmax, cont);
			var singleInput = $(opt.singleinput, cont);
			var singleText = $(opt.singletext, cont);
			inputMin.val(min);
			inputMax.val(max);
			singleInput.val(min);
			textMin.text(inputMin.val());
			textMax.text(inputMax.val());
			singleText.text(singleInput.val());
			el.slider({
				value: range? false : min,
				values: range? [min, max] : false
			});
		}
	});


	// DATEPICKER
	new Component('datepicker', '.datepicker', {}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.datepicker) return;
			el.datepicker($.extend({
				firstDay: 1,
				dateFormat: "dd.mm.yy",
				dayNamesMin: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
				dayNamesShort: ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
				dayNames: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
				monthNames: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]
			}, opt.userOptions));
		}
	});


	// AUTOCOMPLETE
	new Component('autocomplete', '.autocomplete', {
		keytitle: '',
		key: '',
		url: '',
		params: '',
		data: {},
		acOptions: {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.autocomplete) return;
			var cache = {};
			el.autocomplete($.extend({
				source: function(request, response) {
					var term = request.term;
					if (term in cache) {
						response(cache[term]);
						return;
					}
					var req = {};
					if (opt.key) req.key = opt.key;
					if (opt.keytitle) req[opt.keytitle] = request.term;
					else req.term = request.term;
					if (opt.params) {
						var params = opt.params.split(',');
						for (var i in params) {
							var p = params[i].split(':');
							if (p.length > 1) {
								req[p[0]] = p[1];
							}
						}
					}
					$.getJSON(opt.url, $.extend(req, opt.data), function(data) {
						var dataArr = [];
						for (var i in data) {
							dataArr.push(data[i]);
						}
						cache[term] = dataArr;
						response(dataArr);
					});
				}
			}, opt.acOptions));
		}
	});


	// FORMS
	new Component('form', '.user-form', {
		url: '',
		method: 'POST',
		datatype: 'json',
		field: '.field',
		ignoreclass: 'a-hidden',
		ignoreempty: false,
		clearsubmit: true,
		clear: '.clear-form',
		errorslist: '.f-errors',
		formerrorslist: '.form-errors',
		erroritemsprefix: '.error-',
		errorshiddenclass: 'a-hidden',
		errorclass: 'm-error',
		sendingclass: 'sending',
		notajax: false,
		notajaxresponse: 'send',
		slidercont: '.slider-wrap',
		sliderinputmin: '.input-min',
		sliderinputmax: '.input-max',
		data: {},
		ajaxFormOptions: {},
		beforeSubmit: function() {},
		afterSubmit: function(res, isErr) {},
		success: function(res) {},
		errors: function(err) {},
		serverError: function(err) {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			var form = this;
			if (!el.is('FORM')) return;
			el.find(opt.clear).off('click').on('click', function() {
				form.clear(el, opt);
				return false;
			});
			el.find('INPUT, TEXTAREA, SELECT').not('[data-disabled]').removeAttr('disabled');
			el.find('.' + opt.errorclass).removeClass(opt.errorclass);
			el.find(opt.errorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
			el.find(opt.formerrorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
			if (el.attr('method') && !opt.userOptions.method) opt.method = el.attr('method');
			el.off('submit').on('submit', function() {
				if ($(this).hasClass(opt.sendingclass)) return false;
				if (el.hasClass('ui-not-ajax-send')) return true;
				form.submit(el, _.extend(opt, opt.userOptions));
				return false;
			});
		},
		submit: function(el, opt) {
            var form = this;
			if (el.hasClass(opt.sendingclass)) return false;
			opt.data = opt.userOptions.data || {};
			if (!opt.url) opt.url = el.attr('action') || null;
			if (el.data('checkstring')) {
				$('INPUT[name="check_string"]', el).val(el.data('checkstring'));
				$('INPUT[name="hash_string"]', el).val(el.data('hashstring'));
			}
			var before = opt.beforeSubmit.apply(el);
			if (before === false) return false;
			el.find('INPUT, TEXTAREA, SELECT').not('[data-disabled]').removeAttr('disabled');
			el.find('.' + opt.errorclass).removeClass(opt.errorclass);
			el.find(opt.errorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
			el.find(opt.formerrorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
			$('INPUT, TEXTAREA, SELECT', el).each(function() {
				if ($(this).hasClass(opt.ignoreclass) || $(this).closest('.' + opt.ignoreclass).length) {
					$(this).attr('disabled', true);
				}
				if (opt.ignoreempty && $(this).val() === '') {
					$(this).attr('disabled', true);
				}
			});
			if (generalComponents.slider) {
				el.find(generalComponents.slider.el).each(function() {
					var slider = $(this);
					var cont = slider.closest(opt.slidercont);
					var inputMin = $(opt.sliderinputmin, cont);
					var inputMax = $(opt.sliderinputmax, cont);
					if (parseFloat(String(slider.data('min')).replace(',', '.')) === parseFloat(String(inputMin.val()).replace(',', '.'))) {
						inputMin.attr('disabled', true);
					}
					if (parseFloat(String(slider.data('max')).replace(',', '.')) === parseFloat(String(inputMax.val()).replace(',', '.'))) {
						inputMax.attr('disabled', true);
					}
				});
			}
			if (before === opt.notajaxresponse || opt.notajax || !el.ajaxSubmit) {
				el.addClass('ui-not-ajax-send').submit();
				return false;
			}
			var method = opt.method;
			if (el.attr('method') && !opt.userOptions.method) method = el.attr('method');
			el.addClass(opt.sendingclass).ajaxSubmit(_.extend({
				dataType: opt.datatype,
				type: method,
				url: opt.url,
				data: opt.data,
				success: function(res) {
					if (opt.datatype === 'json' && res.errors) {
						opt.afterSubmit.call(el, res.errors, true);
						opt.errors.call(el, res.errors, res);
						for (var e in res.errors) {
							var input = el.find('[name="' + (res.errors[e].key || e) + '"]');
							var field = input.closest(opt.field);
							input.addClass(opt.errorclass);
							field.addClass(opt.errorclass);
							field.find(opt.erroritemsprefix + (res.errors[e].error || res.errors[e])).removeClass(opt.errorshiddenclass);
							field.find(opt.errorslist).removeClass(opt.errorshiddenclass);
						}
					} else {
						el.find('.' + opt.errorclass).removeClass(opt.errorclass);
						el.find(opt.errorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
						el.find(opt.formerrorslist).addClass(opt.errorshiddenclass).find('> *').addClass(opt.errorshiddenclass);
						opt.afterSubmit.call(el, res, false);
						opt.success.call(el, res);
                        form.sendMetricsEvent();
					}
					$('INPUT, TEXTAREA, SELECT', el).not('[data-disabled]').removeAttr('disabled');
					el.removeClass(opt.sendingclass);
				},
				error: function(err) {
					opt.afterSubmit.call(el, err, true);
					opt.serverError.call(el, err);
					el.find(opt.formerrorslist).removeClass(opt.errorshiddenclass)
						.find(opt.erroritemsprefix + err.status).removeClass(opt.errorshiddenclass);
					el.removeClass(opt.sendingclass);
					$('INPUT, TEXTAREA, SELECT', el).not('[data-disabled]').removeAttr('disabled');
				}
			}, opt.ajaxFormOptions));
		},
		clear: function(el, opt) {
			if (el.clearForm) el.clearForm();
			if (generalComponents.slider) {
				var sliders = el.find(generalComponents.slider.el);
				generalComponents.slider.clear(sliders);
			}
			$('SELECT', el).each(function() {
				var empty = null;
				$('OPTION', this).each(function() {
					if (!$(this).attr('value')) {
						empty = $(this);
						return false;
					}
				});
				if (!empty) empty = $('OPTION:first', this);
				empty.attr('selected', true);
			});
			$('INPUT, TEXTAREA, SELECT', el).change();
			if (opt.clearsubmit) el.submit();
		},
		sendMetricsEvent: function() {
            var personSearchEvent = 'PERSON_SEARCH',
                sellFlatEvent = 'SELL_FLAT',
                contactsEvent = 'CONTACTS',
                realEstateEvent = 'REAL_ESTATE',
                resaleEvent = 'RESALE',
                lookFlatEvent = 'LOOK_FLAT',
                curUrl = window.location.href,
                locationAsArray = _.compact(curUrl.split('/')),
                query = locationAsArray[2],
                subQuery = locationAsArray[3],
                eventName = '';

            switch (query) {
                case 'selection':
                    eventName = personSearchEvent;
                    break;
                case 'contacts':
                    eventName = contactsEvent;
                    break;
                case 'real-estate':
                    if (subQuery == 'request') {
                        eventName = realEstateEvent;
                    }
                    break;
                case 'resale':
                    if (subQuery == 'request') {
                        eventName = resaleEvent;
                    }
                    break;
                case 'owner':
            		eventName = sellFlatEvent;
                    break;
                case 'residentional':
                    if (subQuery == 'request') {
                        eventName = lookFlatEvent;
                    }
                    break;
            }
            if (eventName != '') {
                if ('yaCounter33436783' in window && yaCounter33436783.reachGoal) {
    				yaCounter33436783.reachGoal(eventName);
    			}
    			if ('ga' in window) {
		            var trackers = ga.getAll();
                    trName = trackers[0].get('name');
    				ga(trName + '.send', 'event', eventName, 'submit');
    			}
                
                if ('roistat' in window) {
		            roistat.event.send(eventName);
    			}
                
                
			}
		}
	});
	
	// SORTABLE
	new Component('sortable', '.sortable', {
		url: '/',
		notsend: 0,
		cont: '',
		method: 'POST',
		datatype: 'json', 
		handle: '.drag-drop',
		items: '>*',
		itemsexcepts: '.unchangeable',
		oldpositionname: 'oldPosition',
		newpositionname: 'newPosition',
		positionattr: 'position',
		sendattrs: 'id',
		sorted: function() {},
		errors: function () {},
		serverError: function () {},
		sortableOptions: {},
		beforeSend: function() {},
		ajaxOptions: {},
		data: {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.sortable) return;
			var prevItem = 0;
			el.sortable(_.extend({
				items: opt.items + ':not(' + opt.itemsexcepts + ')',
				handle: opt.handle,
				revert: false,
				zIndex: 9999,
				start: function(event, sui) {
					prevItem = sui.item.prev().data(opt.positionattr);
				},
				stop: function(event, sui) {
					if (opt.notsend) {
						return true;
					} else if (prevItem === sui.item.prev().data(opt.positionattr)) {
						el.sortable('cancel');
						return false;
					}
					var sendAttrs = {};
					sendAttrs[opt.oldpositionname] = parseInt(sui.item.data(opt.positionattr));
					if (sendAttrs[opt.oldpositionname] < parseInt(sui.item.next().data(opt.positionattr))){
						sendAttrs[opt.newpositionname] = parseInt(sui.item.prev().data(opt.positionattr));
					} else {
						sendAttrs[opt.newpositionname] = parseInt(sui.item.next().data(opt.positionattr));
					}
					if (opt.sendattrs) {
						var attrs = opt.sendattrs.split(';');
						for (var i in attrs) {
							if (sui.item.data(attrs[i])) {
								sendAttrs[attrs[i]] = sui.item.data(attrs[i]);
							}
						}
					}
					if (sendAttrs[opt.oldpositionname] !== sendAttrs[opt.newpositionname]) {
						if (isNaN(sendAttrs[opt.newpositionname])) {
							sendAttrs[opt.newpositionname] = parseInt(sui.item.prev().data(opt.positionattr));
						}
						sendAttrs = _.extend(sendAttrs, opt.data);
						var cont = opt.cont || el;
						cont = $(cont);
						var beforeSend = opt.beforeSend.call(el, sendAttrs);
						cont.trigger(triggerStr + opt.component + '-beforeSend');
						if (beforeSend === false || !sendAttrs[opt.newpositionname]) {
							el.sortable('cancel');
							return false;
						} else if (_.isObject(beforeSend)) {
							sendAttrs = _.extend(sendAttrs, beforeSend);
						}
						$.ajax(_.extend({
							url: opt.url,
							data: sendAttrs,
							type: opt.method,
							dataType: opt.datatype,
							success: function(res) {
								if (opt.datatype === 'json') {
									if (res.errors) {
										opt.errors.call(el, res.errors, res);
										cont.trigger(triggerStr + opt.component + '-errors');
									} else if (res.content) {
										cont.html(res.content);
										opt.sorted.call(el);
										cont.trigger(triggerStr + opt.component + '-sorted');
									}
								} else {
									cont.html(res);
									opt.sorted.call(el);
									cont.trigger(triggerStr + opt.component + '-sorted');
								}
							}
						}, opt.ajaxOptions)).error(function(err) {
							var cont = opt.cont || el;
							cont = $(cont);
							opt.serverError.call(el, err);
							cont.trigger(triggerStr + opt.component + '-serverError');
						});
					}
				}
			}, opt.sortableOptions));
		}
	});


	// CAROUSEL
	new Component('carousel', '.carousel', {
		speed: 300,
		infinite: 1,
		autoplay: 0,
		slickOptions: {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.slick) return;
			el.slick($.extend({
				speed: opt.speed,
				infinite: opt.infinite? true : false,
				autoplay: opt.autoplay? true : false,
				autoplaySpeed: opt.autoplay
			}, opt.slickOptions));
		}
	});


	// FANCYBOX
	new Component('fancybox', '.fancybox', {
		fancyboxOptions: {}
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.fancybox) return;
			el.fancybox(opt.fancyboxOptions);
		}
	});
	
	// COLORPICKER
	new Component('colorpicker', '.colorpicker', {
		preferredformat: "#hex6",
		changeouterinput: 1,
		allowEmpty: 1,
		showinput: 1,
		showalpha: 0
	}, {
		init: function(el, opt) {
			if (el.closest(ignoreClass).length) return;
			if (!el.spectrum) return;
			var colorpickerBlock = el.closest('.colorpicker-block');
			var colorpickerInput = colorpickerBlock.find('.colorpicker-input');
			var currentColor;
			el.spectrum($.extend({
				preferredFormat: opt.preferredformat,
				showInput: opt.showinput? true : false,
				showAlpha: opt.showalpha? true : false,
				changeouterinput: opt.changeouterinput? true : false,
				allowEmpty: opt.allowEmpty? true : false,
				move: function(color) {
					opt.changeouterinput? colorpickerInput.val(color) : '';	
				},
				beforeShow: function(color) {
					currentColor = color;
				}
			}, opt.userOptions));
			if (opt.changeouterinput) {
				colorpickerInput.val(el.val());
				colorpickerInput.on('keyup', function() {
					el.spectrum('set', $(this).val());
				});
				$('.sp-cancel').click(function() {
					colorpickerInput.val(currentColor);
				});
			}
		}
	});

	/******************** UI ********************/

	var Ui = function(compList) {
		if (!compList || !compList.length) compList = generalComponents;
		var ui = this;
		var registerComp = function(name, comp) {
			ui.components[name] = comp;
			ui[name] = comp.init;
			for (var i in comp) {
				ui[name][i] = comp[i];
			}
		};
		
		ui.components = {};		
		
		ui.addComponent = function(name, defaultOpt, methods) {
			new Component(name, defaultOpt, methods);
			if (generalComponents[name]) {
				registerComp(name, generalComponents[name]);
				ui[name].init();
			}
		};
		
		ui.scrollTo = function(target, opt) {
			if (!target) return false;
			if (!opt) {
				if (typeof target === 'object' && !target.jquery) opt = target;
				else opt = {target: target};
			} else {
				if (typeof opt !== 'object') opt = {};
				opt.target = target;
			}
			var el = $('HTML, BODY');
			var beforeI = 0;
			var afterI = 0;
			var beforeScroll = opt.beforeScroll || null;
			var afterScroll = opt.afterScroll || null;
			opt.beforeScroll = function() {
				if (!beforeScroll) return;
				if (++beforeI === el.length) {
					beforeScroll.apply(el);
				}
			};
			opt.afterScroll = function() {
				if (!afterScroll) return;
				if (++afterI === el.length) {
					afterScroll.apply(el);
				}
			};
			generalComponents.scroller.scroll(el, opt);
		};
		
		ui.clickOut = clickOut;
		
		ui.initAll = function() {
			for (var i in ui.components) {
				ui.components[i].init();
			}
		};
				
		for (var i in compList) {
			if (generalComponents[i]) {
				registerComp(i, generalComponents[i]);
			}
		}
		ui.initAll();
		return ui;
	};

	// события по кликам
	$('BODY').on('click', '.submit', function() {
		if (!$(this).is('BUTTON') && !$(this).is('INPUT:submit')) {
			$(this).closest('FORM').submit();
			return false;
		}
	}).on('click', '[data-toggle]', function() {
		var btn = $(this);
		var toggle = btn.data('toggle').split(':');
		var name = toggle[0];
		var action = toggle[1];
		var target = null;
		if (btn.data('target') && btn.data('target') !== '#') target = $(btn.data('target'));
		else if (btn.attr('href') && btn.attr('href') !== '#') target = $(btn.attr('href'));
		if (!name || !generalComponents[name]) return false;
		if (!action || !generalComponents[name][action]) return false;
		if (!target) {
			if (btn.closest(generalComponents[name].el).length) {
				target = btn.closest(generalComponents[name].el);
			} else {
				return false;
			}
		} else if (!target.length) {
			return false;
		}
		generalComponents[name][action](target);
		return false;
	});

	return new Ui([]);

});