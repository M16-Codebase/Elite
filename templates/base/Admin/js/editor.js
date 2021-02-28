window.redactorCaret = {offset: 0, top: 0};
define(['message'], function(message) {
	var initEditor = function(el) {
		el = el || '.redactor';
		if (!$(el).redactor) {
			return false;
		}
		
		var images = {};
		var dragImgTimer = 0;
		$(el).redactor({
			lang: 'ru',
			focus: false,
			replaceDivs: false,
			buttonSource: true,
			toolbarFixed: false,
			imageUpload: '/images/',
			buttons: ['html', 'formatting', 'bold', 'italic', 'unorderedlist', 'orderedlist', 'alignment', 'link', ''],
			plugins: ['table'],
			formatting: ['p'],
			formattingAdd: [{
				tag: 'p',
				title: 'Заголовок 1',
				class: 'h1',
				clear: true
			}, {
				tag: 'p',
				title: 'Заголовок 2',
				class: 'h2',
				clear: true
			}, {
				tag: 'p',
				title: 'Заголовок 3',
				class: 'h3',
				clear: true
			}, {
				tag: 'h4',
				title: 'Заголовок 4',
				class: 'h4',
				clear: true
			}, {
				tag: 'p',
				title: 'Аннотация',
				class: 'post-annotation',
				clear: true
			}, {
				tag: 'p',
				title: 'Текст слева',
				class: 'm-left',
				clear: true
			}, {
				tag: 'p',
				title: 'Текст справа',
				class: 'm-right',
				clear: true	
			}, {
				tag: 'span',
				title: 'Неразрывный текст',
				class: 'nowrap'
			}],
			initCallback: function() {
				var redactor = this;
				var editor = this.$editor;
				var toolbar = this.$toolbar;
				var textarea = this.$textarea;
				var scroll = editor.closest('.mCustomScrollbar');
				var scrollCont = editor.closest('.content-scroll');
				var initScrollTimer = 0;
				var initScroll = function() {
					if (!scroll.length) {
						scroll = editor.closest('.mCustomScrollbar');
						if (!scroll.length) {
							if (editor.closest('.viewport').length) {
								clearTimeout(initScrollTimer);
								initScrollTimer = setTimeout(initScroll, 1000);
							}
							return;
						}
					}
					clearTimeout(initScrollTimer);
					scroll.on('mScrolling', function(){
						var currentEditor = editor.is(':visible')? editor : textarea;
						var top = currentEditor.offset().top - scroll.offset().top + 22;
						var editorHeight = currentEditor.height();
						var toolbarHeight = toolbar.height();
						if (top < toolbarHeight) {
							if (-top > editorHeight) {
								toolbar.css({top: toolbarHeight+editorHeight});
								scrollCont.removeClass('m-no-top-grad');
							} else {
								toolbar.css({top: toolbarHeight-top});
								scrollCont.addClass('m-no-top-grad');
							}
						} else {
							scrollCont.removeClass('m-no-top-grad');
							toolbar.css({top: '0'});
						}
					});
				};
				initScroll();
				editor.addClass('post-block');
				editor.on('mouseup keyup', function() {
					redactorCaret = {
						offset: redactor.caret.getOffset(),
						top: editor.offset().top
					};
				});
				editor.on('click', '.toggle-fancybox', function() {
					var img = $(this).closest('.content-image').find('IMG');
					var wrap = img.closest('.fancybox');
					if (wrap.length) {
						$(this).removeClass('m-active');
						img.removeClass('m-fancybox');
						wrap.replaceWith(wrap.html());
					} else {
						$(this).addClass('m-active');
						img.addClass('m-fancybox').wrap('<a href="' + img.attr('src') + '" class="fancybox" rel="post_gallery"></a>');
					}
					textarea.change().redactor('code.sync');
				});
				editor.on('click', '.toggle-descr', function() {
					var descr = $(this).closest('.content-image').find('.image-description');
					var img = $(this).closest('.content-image').find('IMG');
					if ($(this).toggleClass('m-active').hasClass('m-active')) {
						img.addClass('m-descr');
						descr.removeClass('a-hidden');
					} else {
						img.removeClass('m-descr');
						descr.addClass('a-hidden');
					}
					textarea.change().redactor('code.sync');
				});
				editor.on('click', '.change-position', function() {
					if ($(this).hasClass('m-active')) return false;
					var imgCont = $(this).closest('.content-image');
					var img = imgCont.find('IMG');
					$(this).addClass('m-active').siblings('.change-position').removeClass('m-active');
					imgCont.removeClass('a-left a-right a-center');
					img.removeClass('m-left m-right m-center');
					if ($(this).hasClass('m-left')) {
						imgCont.addClass('a-left');
						img.addClass('m-left');
					} else if ($(this).hasClass('m-right')) {
						imgCont.addClass('a-right');
						img.addClass('m-right');
					} else {
						imgCont.addClass('a-center');
						img.addClass('m-center');
					}
					textarea.change().redactor('code.sync');
				});
				editor.on('click', '.delete', function() {
					var btn = $(this);
					message.confirm({
						text: 'Подтвердите удаление изображения.',
						type: 'delete',
						ok: function() {
							btn.closest('.content-image').remove();
							textarea.change().redactor('code.sync');
						}
					});
				});
				var imgMove = false;
				editor.on('mousedown', 'IMG', function() {
					imgMove = true;
					clearTimeout(dragImgTimer);
					$(this).closest('.content-image').addClass('m-delete');
				}).on('mouseup', 'IMG', function() {
					var img = $(this);
					clearTimeout(dragImgTimer);
					dragImgTimer = setTimeout(function() {
						img.closest('.content-image').removeClass('m-delete');
						imgMove = false;
					}, 100);
				}).on('mouseup', function() {
					if (imgMove) {
						var img = $('IMG', this);
						clearTimeout(dragImgTimer);
						dragImgTimer = setTimeout(function() {
							img.closest('.content-image').removeClass('m-delete');
							imgMove = false;
						}, 100);
					}
				});
				this.opts.changeCallback.call(this);
			},
			focusCallback: function() {
				return false;
			},
			sourceCallback: function() {
				this.$textarea.blur();
				this.$textarea[0].setSelectionRange(0,0);
				this.$textarea[0].focus();
				$(window).resize();
			},
			syncBeforeCallback: function(html) {
				var div = $('<div />').html(html);
				div.find('.content-image .image-controls').remove();
				return div.html();
			},
			pasteBeforeCallback: function(html) {
				var changeTags = [['div', 'p'], ['b', 'strong'], ['i', 'em']];
				html = this.paragraphize.load(html);
				html = $('<DIV />').html(html);
				for (var i in changeTags) {
					$(changeTags[i][0], html).each(function() {
						var attrs = {};
						$.each(this.attributes, function(idx, attr) {
							attrs[attr.nodeName] = attr.nodeValue;
						});
						$(this).replaceWith(function () {
							return $('<' + changeTags[i][1] + ' \>', attrs).html($(this).html());
						});
					});
				}
				return html.html();
			},
			changeCallback: function changeCallback(h) {
				clearTimeout(dragImgTimer);
				var redactor = this;
				var editor = this.$editor;
				var textarea = this.$textarea;
				redactorCaret = {
					offset: this.caret.getOffset(),
					top: editor.offset().top
				};
				editor.find('IMG').each(function() {
					var img = $(this).uniqueId();
					var src = img.attr('src');
					if (img.closest('.content-image').hasClass('m-delete')) {
						img.closest('.content-image').remove();
						img.remove();
						return;
					}
					
					// Создаём контейнер для картинки
					if (!img.closest('.content-image').length || img.closest('.content-image').data('img-id') !== img.attr('id')) {
						img.wrap('<figure class="content-image" contenteditable="false" data-img-id="' + img.attr('id') + '" />');
						img.after('<figcaption class="image-description a-hidden" contenteditable="true">' + (img.attr('title') || img.attr('alt')) + '</figcaption>');
					} else {
						var newDescrText = img.closest('.content-image').find('.image-description').text();
						if (newDescrText) {
							img.attr('title', newDescrText);
							img.attr('alt', newDescrText);
						}
					}
					var block = img.closest('.content-image');
					var descr = block.find('.image-description');
					
					// Добавляем элементы управления картинкой
					if (!$('.image-controls', block).length) {
						block.append('<div class="image-controls" contenteditable="false"><div class="image-controls-inner"></div></div>');
						$('.image-controls-inner', block)
							.append('<div class="change-position m-left" title="Установить слева"><i> </i></div>')
							.append('<div class="change-position m-right" title="Установить справа"><i> </i></div>')
							.append('<div class="change-position m-center" title="Установить по центру"><i> </i></div>')
							.append('<div class="toggle-descr" title="Показывать описание"><i> </i></div>')
							.append('<div class="toggle-fancybox" title="Увеличивать изображение"><i> </i></div>')
							.append('<div class="delete" title="Удалить изображение"><i> </i></div>');
					}
					if (img.attr('style') && img.attr('style').match(/width\:/gi)) {
						images[img.attr('id')] = img.width();
					} else {
						img.width(images[img.attr('id')]);
					}
					
					// Увеличение картинки
					if (img.hasClass('m-fancybox')) {
						if (!img.closest('.fancybox').length) {
							img.wrap('<a href="' + img.attr('src') + '" class="fancybox" rel="post_gallery"></a>');
						}
						$('.image-controls .toggle-fancybox', block).addClass('m-active');
					} else {
						if (img.closest('.fancybox').length) {
							var wrap = img.closest('.fancybox');
							wrap.replaceWith(wrap.html());
						}
						$('.image-controls .toggle-fancybox', block).removeClass('m-active');
					}
					
					// Показывает подпись
					if (descr.text()) {
						if (img.hasClass('m-descr')) {
							img.addClass('m-descr');
							descr.removeClass('a-hidden');
							$('.image-controls .toggle-descr', block).addClass('m-active');
						} else {
							img.removeClass('m-descr');
							descr.addClass('a-hidden');
							$('.image-controls .toggle-descr', block).removeClass('m-active');
						}
						$('.image-controls .toggle-descr', block).removeClass('a-hidden');
					} else {
						img.removeClass('m-descr');
						descr.addClass('a-hidden');
						$('.image-controls .toggle-descr', block).addClass('a-hidden');
					}
					descr.width(images[img.attr('id')]);
					
					// Добавляем нужное обтекание
					block.removeClass('a-left a-right a-center');
					if (img.attr('style') && img.attr('style').match(/float\:\s?left/gi)) {
						img.addClass('m-left').removeClass('m-right m-center');
						block.addClass('a-left');
					} else if (img.attr('style') && img.attr('style').match(/float\:\s?right/gi)) {
						img.addClass('m-right').removeClass('m-left m-center');
						block.addClass('a-right');
					} else if (img.attr('style') && img.attr('style').match(/margin\:\s?auto/gi)) {
						img.addClass('m-center').removeClass('m-left m-right');
						block.addClass('a-center');
					} else if (img.hasClass('m-left')) {
						block.addClass('a-left');
					} else if (img.hasClass('m-right')) {
						block.addClass('a-right');
					} else if (img.hasClass('m-center')) {
						block.addClass('a-center');	
					}
					img.removeAttr('rel');
					if (img.attr('style')) {
						img.attr('style', img.attr('style').replace(/(margin|display|float)\:[\s\w]+(;|$)?/gi, ''));
					}
					if (block.hasClass('a-left')) {
						$('.image-controls .change-position.m-left', block).addClass('m-active');
					} else if (block.hasClass('a-right')) {
						$('.image-controls .change-position.m-right', block).addClass('m-active');
					} else {
						$('.image-controls .change-position.m-center', block).addClass('m-active');
					}
					
					// Переносим картинку в начало абзаца
					if (block.closest($('LI, TD, TH', editor)).length) {
						var parentBlock = block.closest($('LI, TD, TH', editor));
						parentBlock.prepend(block);
					} else if (block.closest($('>*', editor)).length) {
						var parentBlock = block.closest($('>*', editor));
						if (!parentBlock.is(block)) {
							parentBlock.before(block);
						}
					} else  {
						var parentBlock = editor;
					}
					redactor.image.setEditable(img);
				});
				$(this.$editor).find('.content-image').each(function() {
					if (!$('IMG', this).length) {
						$(this).remove();
					}
				});
				$(window).scroll().resize();
			}
		});
	};
	initEditor();
	
	return initEditor;
});