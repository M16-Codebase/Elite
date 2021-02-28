/*
	editContent.open(opt)
		opt.form
		opt.getform
		opt.getformdata
		opt.getformtype
		opt.getformmethod
		opt.class
		opt.attr
		opt.clearform
		opt.formdata
		opt.loadform
		opt.beforeClose
		opt.customform
	editContent.close()
 */

define(['ui', 'message', 'filter'], function(ui, message, filter) {
	var main = $('.main-content-inner');
	var editContent = {};
	var openTime = 600;
	var openTimer = 0;
	var animation = false;
	var current = $('.edit-content.m-edit-open', main).length? $('.edit-content.m-edit-open', main) : $('.view-content', main);
	var prev = $('.view-content', main);
	history.replaceState({id: current.uniqueId().attr('id')}, '', location.href);
	
	editContent.open = function(opt) {
		if (!opt || animation) return false;
		animation = true;
		var cont = {};
		var form = {};
		if (opt.form) {
			form = $(opt.form);
			if (!form.length) {
				animation = false;
				return false;
			}
			form = form.clone(true).removeClass('a-hidden').show();
			cont = $('<div />', {class: 'edit-content'});
			cont.html(form);
			main.append(cont);
			ui.initAll();
			initForm();
		} else if (opt.getform) {
			opt.getformdata = opt.getformdata || {};
			$.ajax({
				url: opt.getform, 
				type: (opt.getformmethod && opt.getformmethod.toLowerCase() === 'get')? 'GET' : 'POST',
				data: opt.getformdata,
				dataType: opt.getformtype
			}).done(function(res) {
				var content = res;
				if (opt.getformtype && opt.getformtype.toLowerCase() === 'json') {
					if (res.errors) {
						message.errors(res);
						animation = false;
						return false;
					}
					content = res.content;
				}
				if (!res) {
					message.errors('Пустой ответ сервера.');
					animation = false;
					return false;
				}
				cont = $('<div />', {class: 'edit-content'});
				cont.html(content);
				main.append(cont);
				ui.initAll();
				initForm(res);
			}).error(function(err) {
				animation = false;
				message.serverErrors(err);
			});
		}
		function initForm(res) {
			prev = $('.edit-content.m-edit-open', main).length? $('.edit-content.m-edit-open', main) : $('.view-content', main);
			history.replaceState({id: prev.uniqueId().attr('id')}, '', location.href);
			if ($('[data-site-url]', cont).length) {
				$('.page-header .to-site').attr('href', $('[data-site-url]', cont).data('site-url')).removeClass('a-hidden');
			} else {
				$('.page-header .to-site').addClass('a-hidden');
			}
			if (!opt.customform && $('FORM', cont).length < 2) {
				if (!$('FORM', cont).length) {
					cont.wrapInner('<form></form>');
				}
				form = $('FORM', cont);
				if (opt.class) form.addClass(opt.class);
				if (opt.attr) form.attr(opt.attr);
				if (opt.clearform) {
					form.clearForm();
				}
				if (opt.formdata) {
					for (var i in opt.formdata) {
						form.find('[name="' + i + '"]').val(opt.formdata[i]);
					}
				}
				if (opt.loadform) {
					opt.loadform.call(form, res);
				}
				if (!opt.errors) {
					opt.errors = function(errors) {
						var errText = opt.errorsText || {};
						errText['obj:already_changed'] = 'В данный объект были внесены изменения. Необходимо обновить страницу.';
						message.errors({
							errorsText: errText,
							errors: errors
						});
					};
				}
				if (!opt.serverError) {
					opt.serverError = function(err) {
						message.serverErrors(err);
					};
				}
				if (!opt.customform) {
					ui.form(form, opt);
				}
			} else {
				if (opt.class) cont.addClass(opt.class);
				if (opt.attr) cont.attr(opt.attr);
				if (opt.loadform) {
					opt.loadform.call(cont);
				}
			}
			cont.data('beforeclose', (opt.beforeClose || null));
			prev = current;
			current = cont;
			current.data('prev', prev);
			main.addClass('m-edit');
			clearTimeout(openTimer);
			setTimeout(function() {
				var id = cont.uniqueId().attr('id');
				var url = $('.tabs .tab-title.m-current', cont).attr('href') || cont.data('history-url') || location.href;
				history.pushState({id: id}, '', url);
				cont.siblings().removeClass('m-current');
				cont.addClass('m-edit-open m-current');
				$(window).resize();
				animation = false;
				filter();
			}, 50);
		};
	};
	editContent.close = function(callback, changeState) {
		var close = current.data('beforeclose')? current.data('beforeclose').call(current) : null;
		var prevCont = current.data('prev') || prev;
		if ($('[data-site-url]', prevCont).length) {
			$('.page-header .to-site').attr('href', $('[data-site-url]', prevCont).data('site-url')).removeClass('a-hidden');
		} else {
			$('.page-header .to-site').addClass('a-hidden');
		}
		if (current.is('.view-content') || animation || close === false) {
			history.pushState({id: current.attr('id')}, '', location.href);
			return false;
		}
		if (!changeState) {
			history.back();
		} else {
			animation = true;
			current.removeClass('m-edit-open');
			if (!$('.edit-content.m-edit-open', main).length) {
				main.removeClass('m-edit');
			}
			prevCont.css({opacity: 1});
			callback = callback || function() {};
			clearTimeout(openTimer);
			openTimer = setTimeout(function() {
				prevCont.css({opacity: ''}).addClass('m-current');
				current.remove();
				current = prevCont;
				prev = current.data('prev');
				animation = false;
				$(window).resize();
				message.close();
				callback();
			}, openTime);
		}
		filter();
	};
	
	editContent.animation = function() {
		return animation;
	};
	
	main.on('click', '.edit-content .actions-panel .action-back', function() {
		history.back();
		return false;
	});
	window.addEventListener('popstate', function(e) {
		var prevCont = prev;
		if (!prevCont.is('.view-content') && e.state && e.state.id) {
			if (!prevCont) {
				prevCont = $('#' + e.state.id).length? $('#' + e.state.id) : $('.view-content', main);
			}
			current.data('prev', prevCont);
		} else {
			var url = $('.view-content .tabs .tab-title.m-current', main).attr('href') || $('.view-content', main).data('history-url') || location.href;
			history.replaceState({id: $('.view-content', main).uniqueId().attr('id')}, '', url);
			current.data('prev', $('.view-content', main));
		}
		editContent.close(null, true);
	}, false);
	
	return editContent;
});