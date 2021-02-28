define(['ui'], function(ui) {
	var errors = {};
	var confirm = {};
	var ok = {};
	
	var openClass = 'm-open-message';
	var openDescrClass = 'm-open-descr';
	var errorTarget = 'm-message-error';
	var confirmTarget = 'm-message-confirm';
	var okShow = 4000;
	var okTimer = 0;
	var speed = 300;
	
	var init = function() {
		if (!$('.message.message-errors').length) {
			var el = $('<div />', {class: 'message message-errors a-hidden'});
			el.html('<div class="message-float"><div class="page-center"><div class="message-inner row">'+
					'<div class="message-main w9"><span class="num"></span><span class="text"></span></div>'+
					'<div class="action action-descr w1" title="Подробнее"><i class="icon-prop-more"></i></div>'+
					'<div class="action action-next w1 m-border" title="Следующая ошибка"><i class="icon-message-next"></i></div>'+
					'<div class="action action-close w1 m-border" title="Закрыть"><i class="icon-message-close"></i></div>'+
					'</div></div><div class="message-descr-cont a-hidden"><div class="page-center">'+
					'<div class="message-descr"></div></div></div></div>');
			$('.page-header').after(el);
		}
		if (!$('.message.message-confirm').length) {
			var el = $('<div />', {class: 'message message-confirm a-hidden'});
			el.html('<div class="page-center"><div class="message-inner row">'+
					'<div class="message-main w6"><span class="text"></span></div>'+
					'<div class="action action-confirm w2"><span class="ok-text">Подтвердить</span></div>'+
					'<div class="action action-cancel action-close m-border w2"><span class="cancel-text">Отменить</span></div>'+
					'<div class="w1"></div>'+
					'<div class="action action-close w1 m-border" title="Закрыть"><i class="icon-message-close"></i></div>'+
					'</div></div>');
			$('.page-main').after(el);
		}
		if (!$('.message.message-ok').length) {
			var el = $('<div />', {class: 'message message-ok a-hidden'});
			el.html('<div class="page-center"><div class="message-inner row">'+
					'<div class="message-main w10"><span class="text"></span></div>'+
					'<div class="w1"></div>'+
					'<div class="action action-close w1 m-border" title="Закрыть"><i class="icon-message-close"></i></div>'+
					'</div></div>');
			$('.page-main').after(el);
		}
		errors = $('.message.message-errors');
		confirm = $('.message.message-confirm');
		ok = $('.message.message-ok');
		$(window).resize();
		ui.initAll();
	};
	
	var message = {
		errors: function(opt) {
			init();
			if (!opt) {
				opt = [{text: 'Ошибка!'}];
			} else if (typeof opt !== 'object') {
				opt = [{text: opt}];
			} else if (opt.errors) {
				var err = [];
				opt.errorsText = opt.errorsText || {};
				if (typeof opt.errors === 'string') {
					err.push({text: opt.errors});
				} else {
					var checkError = function(key, val, list) {
						if (typeof val === 'object' && !(val.error && val.key)) {
							for (var i in val) {
								checkError(i, val[i], val);
							}
						} else {
							var addError = true;
							if (typeof val === 'object') {
								var errKey = val.key +':'+ val.error;
								if (!opt.errorsText[errKey] && opt.errorsTextHandler) {
									var handlerResult = opt.errorsTextHandler(val, key, list, opt.errors);
									if (handlerResult && typeof handlerResult === 'string') {
										opt.errorsText[errKey] = handlerResult;
									} else if (handlerResult === false) {
										 addError = false;
									}
								}
							} else {
								var errKey = key +':'+ val;
								if (!opt.errorsText[errKey] && opt.errorsTextHandler) {
									var handlerResult = opt.errorsTextHandler({
										key: key,
										error: val
									}, key, list, opt.errors);
									if (handlerResult && typeof handlerResult === 'string') {
										opt.errorsText[errKey] = handlerResult;
									} else if (handlerResult === false) {
										 addError = false;
									}
								}
							}
							if (addError) {
								err.push({
									text: opt.errorsText[errKey]? opt.errorsText[errKey] : errKey,
									target: $('[name="' + (val.key || key) + '"]').closest('.wblock')
								});
							}
						}
					};
					for (var e in opt.errors) {
						checkError(e, opt.errors[e], opt.errors);
					}
				}
				opt = err;
			} else if ((typeof opt === 'object') && !(opt instanceof Array)) {
				opt = [opt];
			} else {
				for (var e in opt) {
					if (typeof opt[e] !== 'object') {
						opt[e] = {text: opt[e]};
					}
				}
			}
			if (opt.length > 1) {
				$('.message-main', errors).removeClass('w9 w10').addClass('w9');
				$('.action-next', errors).removeClass('a-hidden');
				$('.num', errors).removeClass('a-hidden');
			} else {
				$('.message-main', errors).removeClass('w9 w10').addClass('w10');
				$('.action-next', errors).addClass('a-hidden');
				$('.num', errors).addClass('a-hidden');
			}
			var i = 0;
			var showErr = function(j) {
				if (j >= opt.length) j = 0;
				else if (j < 0) j = opt.length - 1;
				i = j;
				$('.num', errors).text((j+1) + ' / ' + opt.length + ' — ');
				$('.text', errors).text(opt[j].text || 'Ошибка!');
				$('.message-descr-cont', errors).stop(true, true).slideUp(speed, function() {
					errors.removeClass(openDescrClass);
					if (opt[j].descr) {
						$('.action-descr', errors).removeClass('m-inactive');
						$('.message-descr', errors).html(opt[j].descr);
					} else {
						$('.action-descr', errors).addClass('m-inactive');
						$('.message-descr', errors).html('');
					}
				});
				$('.' + errorTarget).removeClass(errorTarget);
				if (opt[j].target) {
					var target = $('.view-content, .edit-content.m-current').find(opt[j].target);
					target.addClass(errorTarget);
					target.closest('.mCustomScrollbar').mCustomScrollbar('scrollTo', target);
				}
			};
			$('.action-next', errors).off().on('click', function() {
				if ($(this).hasClass('m-inactive')) return false;
				showErr(++i);
				return false;
			});
			$('.action-prev', errors).off().on('click', function() {
				if ($(this).hasClass('m-inactive')) return false;
				showErr(--i);
				return false;
			});
			$('.action-descr', errors).off().on('click', function() {
				if ($(this).hasClass('m-inactive')) return false;
				if (errors.hasClass(openDescrClass)) {
					$('.message-descr-cont', errors).stop(true, true).slideUp(speed);
					errors.removeClass(openDescrClass);
				} else {
					$('.message-descr-cont', errors).stop(true, true).slideDown(speed);
					errors.addClass(openDescrClass);
				}
				return false;
			});
			$('.action-close', errors).off().on('click', function() {
				$('.' + errorTarget).removeClass(errorTarget);
				$('.message-float', errors).stop(true, true).slideUp(speed);
				errors.stop(true, true).slideUp(speed, function() {
					$(this).removeClass(openClass).addClass('a-hidden');
					$(window).resize();
				});
			});
			showErr(i);
			$('.message-float', errors).stop(true, true).hide().slideDown(speed);
			errors.addClass(openClass).stop(true, true).slideDown(speed, function() {
				$(this).removeClass('a-hidden');
				$(window).resize();
			});
		},
		
		serverErrors: function(opt) {
			message.errors({
				text: 'Ошибка сервера: ' + opt.status,
				descr: (opt.status === 403 || opt.status === 404 || opt.status === 200)? '' : opt.responseText
			});
		},
		
		confirm: function(opt) {
			init();
			if (typeof opt !== 'object') return false;
			$('.text', confirm).text(opt.text || 'Подтвердите действие.');
			$('.ok-text', confirm).text(opt.okText || 'Подтвердить');
			$('.cancel-text', confirm).text(opt.cancelText || 'Отмена');
			$('.confirm-icon', confirm).remove();
			$('.ok-text', confirm).before('<i class="confirm-icon icon-' + (opt.icon || 'check') + '"></i>');
			if (opt.type) {
				if (opt.type === 'delete') {
					if (!opt.okText) $('.ok-text', confirm).text('Удалить');
					if (!opt.icon) {
						$('.confirm-icon', confirm).remove();
						$('.ok-text', confirm).before('<i class="confirm-icon icon-prop-delete"></i>');
					}
				}
			}
			$('.' + confirmTarget).removeClass(confirmTarget);
			if (opt.target) {
				$(opt.target).addClass(confirmTarget);
			}
			$('.action-confirm', confirm).off().on('click', function() {
				if (opt.ok) opt.ok.call(confirm);
				$('.' + confirmTarget).removeClass(confirmTarget);
				confirm.stop(true, true).slideUp(speed, function() {
					$(this).removeClass(openClass).addClass('a-hidden');
				});
				return false;
			});
			$('.action-close', confirm).off().on('click', function() {
				if (opt.cancel) opt.cancel.call(confirm);
				$('.' + confirmTarget).removeClass(confirmTarget);
				confirm.stop(true, true).slideUp(speed, function() {
					$(this).removeClass(openClass).addClass('a-hidden');
				});
				return false;
			});
			confirm.addClass(openClass).stop(true, true).slideDown(speed, function() {
				$(this).removeClass('a-hidden');
			});
		},
		
		ok: function(opt) {
			init();
			if (typeof opt !== 'object') {
				opt = {text: opt};
			}
			$('.text', ok).text(opt.text || 'Действие успешно завершено.');
			$('.action-close', ok).off().on('click', function() {
				clearTimeout(okTimer);
				ok.stop(true, true).slideUp(speed, function() {
					$(this).removeClass(openClass).addClass('a-hidden');
				});
				return false;
			});
			clearTimeout(okTimer);
			okTimer = setTimeout(function() {
				$('.action-close', ok).click();
			}, okShow);
			ok.addClass(openClass).stop(true, true).slideDown(speed, function() {
				$(this).removeClass('a-hidden');
			});
		},
		
		close: function() {
			clearTimeout(okTimer);
			$('.' + errorTarget).removeClass(errorTarget);
			$('.' + confirmTarget).removeClass(confirmTarget);
			$('.message').stop(true, true).slideUp(speed, function() {
				$(this).removeClass(openClass).addClass('a-hidden');
				$('.message-descr-cont', this).hide();
				$(window).resize();
			});
		}
	};
	message.error = message.errors;
	
	init();
	return message;
});