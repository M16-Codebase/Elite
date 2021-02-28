define(['ui', 'message', 'editor'], function(ui, message) {
	
	var initGallery = function(galleries) {
		galleries = galleries || '.img-uploader-gallery';
		galleries = $(galleries);
		if (!galleries.length) return;
		galleries.each(function() {
			var gallery = $(this);
			if (gallery.data('inited')) return;
			gallery.data('inited', true);
			var tmp = gallery.closest('.tmp-gallery').length? true : false;
			var colId = gallery.data('collection-id') || gallery.data('temp_dir');
			
			// отправка форм
			var sendImageForm = function(form, input) {
				if (input) {
					if (input.closest('.add-new-image').length) {
						var img = input.closest('.add-new-image');
					} else {
						var img = input.closest('.uploaded-image');
					}
				}
				form.parent().append(form.clone());
				var newForm = $('<form />').append(form.removeClass('a-hidden'));
				ui.form.submit(newForm, {
					url: tmp? form.data('action').replace('/images/', '/tmp-images/') : form.data('action'),
					method: 'POST',
					success: function(res) {
						$('#collection-' + colId).html(res.content);
						$(window).resize();
						ui.initAll();
					},
					errors: function(errors) {
						$('.uploaded-image.m-preloader', gallery).remove();
						message.errors({errors: errors});
					},
					serverError: function(err) {
						message.serverErrors(err);
					},
					afterSubmit: function() {
						newForm.remove();
					},
					ajaxFormOptions: {
						uploadProgress: function(e, s, t, p) {
							if (!input) return;
							if (input.data('loading')) {
								img.find('.preloader DIV').css({width: p + '%'});
							} else {
								input.data('loading', true);
								if (img.is('.add-new-image')) {
									var temp = $('<li />', {class: 'uploaded-image m-preloader'})
										.html('<div class="image-inner"><div class="gallery-top"></div><div class="gallery-image"><div class="preloader"><div></div></div></div></div>');
									img.before(temp);
									img = temp;
								} else {
									img.find('.gallery-image').html('<div class="preloader"><div></div></div>');
								}
							}
						}
					}
					
				});
			};
			
			// множественная загрузка
			var multiUpload = function(form, files, callback) {
				if (!form.length || !files.length) return;
				var errors = false;
				var toUpload = [];
				var ready = 0;
				for (var i = 0; i < files.length; i++) {					
					var file = files[i];
					if (!file.type.match(/^image\//i)) {
						errors = true;
						continue;
					}
					var fd = new FormData();
					fd.append('id', colId);
					fd.append('image', file);
					toUpload.push(fd);
				}
				if (errors) message.errors('Можно загружать только изображения.');
				callback = callback || function() {};
				for (var f in toUpload) {
					uploadFile(form, toUpload[f], function(res) {
						ready++;
						if (ready === toUpload.length) {
							console.log('all')
							$('#collection-' + colId).html(res.content);
							$(window).resize();
							ui.initAll();
							callback();
						}
					});
				}
			};
			var uploadFile = function(form, fd, callback) {
				var preloader = null;
				var xhr = new XMLHttpRequest();
				callback = callback || function() {};
				xhr.upload.addEventListener('progress', function(event) {
					if (!preloader) return;
					var percent = parseInt(event.loaded / event.total * 100);
					if (percent > 80) percent -= 3;
					preloader.find('.preloader DIV').css({width: percent + '%'});
				}, false);
				xhr.onreadystatechange = function(e) {
					if (e.target.readyState === 1 && !preloader) {
						preloader = $('<li />', {class: 'uploaded-image m-preloader'})
							.html('<div class="image-inner"><div class="gallery-top"></div><div class="gallery-image"><div class="preloader"><div></div></div></div></div>');
						$('.uploaded-image:last', gallery).after(preloader);
					} else if (e.target.readyState === 4) {
						if (e.target.status === 200) {
							try {
								var res = JSON.parse(xhr.responseText);
							} catch(e) {
								message.errors('Неверный ответ сервера.');
								return;
							}
							var content = $('<div />').html(res.content);
							var newImg = $('.uploaded-image:last', content);
							preloader.replaceWith(newImg);
							callback(res);
						} else {
							message.errors('Ошибка при загрузке файла.');
						}
					};
				};
				xhr.open('POST', tmp? form.data('action').replace('/images/', '/tmp-images/') : form.data('action'));
				xhr.send(fd);
			};

			// добавление и изменение картинки
			gallery.on('change', 'INPUT:file', function() {
				var input = $(this);
				var form = input.closest('.image-form');
				if (input.attr('multiple')) {
					multiUpload(form, input[0].files);
				} else {
					sendImageForm(form, input);
				}
				return false;
			});
			
			// перетаскивание картинок
			var dropzone = gallery.closest('.dropzone, .content-scroll');
			if (FormData && dropzone.length) {	
				dropzone.addClass('dropable');
				dropzone[0].ondragover = function(e) {
					if ($(e.target).closest('.redactor-box').length) return;
					e.preventDefault();
					dropzone.addClass('m-hover');
				};
				dropzone[0].ondragleave = function(e) {
					if ($(e.target).closest('.redactor-box').length) return;
					e.preventDefault();
					dropzone.removeClass('m-hover');
				};
				dropzone[0].ondrop = function(e) {
					if ($(e.target).closest('.redactor-box').length) return;
					e.preventDefault();
					var form = $('.add-new-image .image-form', this);
					multiUpload(form, e.dataTransfer.files);
					dropzone.removeClass('m-hover');
				};
			}

			// удаление картинки
			gallery.on('click', '.option.delete', function() {
				if ($(this).closest('.itemsList').length) return true;
				var image = $(this).closest('.uploaded-image');
				var imageUrl = $('.gallery-image .fancybox', image).attr('href');
				message.confirm({
					text: 'Подтвердите удаление изображения.',
					target: image,
					type: 'delete',
					ok: function() {
						$.post((tmp? '/tmp-images/delete/': '/images/delete/'), {
						id: colId,
						image_id: image.data('image-id'),
						gallery_dir: $('INPUT[name=gallery_dir]', image).val(),
						gallery_data: $('INPUT[name=gallery_data]', image).val(),
						filename: $('INPUT[name=filename]', image).val()
					}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							var redactor = image.closest('.post-images-uploader').prev().find('.redactor, .redactor-init');
							var body = $('<div />').html(redactor.redactor('code.get'));
							var images = $('IMG[src="' + imageUrl + '"]', body);
							images.closest('.content-image').remove();
							images.remove();
							redactor.redactor('code.set', body.html());
							$(gallery).html(res.content);
							ui.initAll();
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
					} 
				});
			});

			// вставка картинки в редактор
			gallery.on('mousedown', '.paste-button', function() {
				if (gallery.hasClass('m-no-paste')) return false;
				if ($(this).closest('.uploaded-item').length) return false;
				var image = $(this).closest('.uploaded-image');
				var imageUrl = $('.gallery-image .fancybox', image).attr('href');
				var description = $('[name="image_text"]', image).val();
				var floatClass = 'm-center';
				var now = Date.now();
				if ($(this).hasClass('paste-left')) floatClass = 'm-left';
				else if ($(this).hasClass('paste-right')) floatClass = 'm-right';
				var insertImage = $('<img>', {
					class: floatClass,
					width: 300,
					src: imageUrl,
					alt: description,
					title: description,
					'data-t': now
				});
				var redactor = $(this).closest('.post-images-uploader').prev().find('.redactor, .redactor-init');
				var scroll = $(this).closest('.mCustomScrollbar');
				redactor.redactor('caret.setOffset', redactorCaret.offset);
				redactor.redactor('insert.node', insertImage[0]);
				redactor.change().redactor('code.sync');
				redactor.redactor('image.setEditable', insertImage);
				var img = $(this).closest('.viewport').find('[data-t="' + now + '"]').removeAttr('data-t');
				if (scroll.length) scroll.mCustomScrollbar('scrollTo', img);
				return false;
			});

			// изменение gravity
			gallery.on('click', '.option.gravity', function() {
				var image = $(this).closest('.uploaded-image');
				if (image.hasClass('m-gravity')) {
					image.removeClass('m-gravity');
				} else {
					var img = $('.gallery-image IMG', image);
					$('.gravity-table', image).css({
						height: img.height() + 2
					});
					image.addClass('m-gravity');
					$(document).one('mousedown', function(e) {
						if ($(e.target).is('.option.gravity') && $(e.target).closest(image).length) return;
						if ($(e.target).closest('.gravity-table', image).length) {
							var gravity = $(e.target).data('gravity');
							$.post((tmp?'/tmp-images/setGravity/' : '/images/setGravity/'), {
								image_id: image.data('image-id'),
								gravity: gravity,
								gallery_dir: $('INPUT[name=gallery_dir]', image).val(),
								gallery_data: $('INPUT[name=gallery_data]', image).val(),
								filename: $('INPUT[name=filename]', image).val()
							}, function(res) {
								if (res.errors) {
									message.errors(res);
								} else {
									gallery.html(res.content);
									ui.initAll();
								}
							}, 'json').error(function(err) {
								message.serverErrors(err);
							});
						}
						image.removeClass('m-gravity');
					});
				}
			});	

			// сортировка		
			gallery.sortable({
				revert: false,
				handle: '.drag-drop',
				items: '.uploaded-image',
				update: function(event, sui) {
					var imageId = parseInt($(sui.item).data('image-id'));
					var oldPosition = parseInt($(sui.item).data('image-position')); //был номер позиции
					var newPosition = parseInt($(sui.item.next()).data('image-position')); //номер позиции следующего элемента
					if (isNaN(newPosition) || oldPosition < newPosition){ //если isNaN следующий элемент, значит переместили в конец (следовательно old < new)
						newPosition = parseInt($(sui.item.prev()).data('image-position'));
					}
					if (!newPosition) return;
					$.ajax({
						url: tmp? '/tmp-images/changePosition/' : '/images/changePosition/',
						dataType: 'json',
						type: 'POST',
						data: ({
							image_id: imageId,
							position: newPosition,
							gallery_dir: $('INPUT[name=gallery_dir]', sui.item).val(),
							gallery_data: $('INPUT[name=gallery_data]', sui.item).val(),
							filename: $('INPUT[name=filename]', sui.item).val()
						}),
						success: function(res) {
							if (res.errors) {
								message.errors(res);
							} else {
								gallery.html(res.content);
								ui.initAll();
							}
						},
						error: function(err) {
							message.serverErrors(err);
						}
					});
				}
			});

			// делаем обложкой одно из изображений
			gallery.on('change', '.set-cover INPUT', function() {
				if ($(this).is(':checked')) {
					var image = $(this).closest('.uploaded-image');
					$.post((tmp? '/tmp-images/setCover/' : '/images/setCover/'), {
						image_id: image.data('image-id'),
						gallery_dir: $('INPUT[name=gallery_dir]', image).val(),
						gallery_data: $('INPUT[name=gallery_data]', image).val(),
						filename: $('INPUT[name=filename]', image).val()
					}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							gallery.html(res.content);
							ui.initAll();
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				}
			});

			// делаем изображение невидимым в выводе галереи
			gallery.on('change', '.set-gallery INPUT', function() {
				var hidden = $(this).is(':checked') ? 0 : 1;
				var image = $(this).closest('.uploaded-image');
				$.post((tmp? '/tmp-images/changeHidden/' : '/images/changeHidden/'), {
					image_id: image.data('image-id'),
					hidden: hidden,
					gallery_dir: $('INPUT[name=gallery_dir]', image).val(),
					gallery_data: $('INPUT[name=gallery_data]', image).val(),
					filename: $('INPUT[name=filename]', image).val()
				}, function(res) {
					if (res.errors) {
						message.errors(res);
					} else {
						gallery.html(res.content);
						ui.initAll();
					}
				}, 'json').error(function(err) {
					message.serverErrors(err);
				});
			});

			// изменение подписи
			gallery.on('click', '.option.img-descr', function() {
				var image = $(this).closest('.uploaded-image');
				var text = $(this).attr('data-descr');
				var opened = image.hasClass('m-open');
				$('.uploaded-image', gallery).removeClass('m-open');
				if (!opened) {
					$('.img-descr-form TEXTAREA', image).text(text);
					image.addClass('m-open');
				}
				setTimeout(function() {
					$(window).resize();
				}, 300);
				return false;
			}).on('click', '.save-descr', function() {
				sendImageForm($(this).closest('.img-descr-form'));
				return false;
			}).on('click', '.close-descr', function() {
				$(this).closest('.uploaded-image').removeClass('m-open');
				setTimeout(function() {
					$(window).resize();
				}, 300);
				return false;
			}).on('click', '.hidden-row .clear-descr', function() {
				message.confirm({
					text: 'Подтвердите удаление подписи.',
					type: 'delete',
					ok: function() {
						$(this).closest('.hidden-row').find('TEXTAREA').text('');
						sendImageForm($(this).closest('.img-descr-form'));
					} 
				});
				return false;
			});
		});
	};
	
	ui.clickOut('.option.img-descr, .hidden-row', function(e) {
		if ($(e.target).is('.option.img-descr, .hidden-row') || $(e.target).closest('.option.img-descr, .hidden-row').length) return false;
		$('.uploaded-image').removeClass('m-open');
	});
	
	initGallery();

	return initGallery;
});