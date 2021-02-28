$(function() {
    require(['ui', 'editContent', 'message', 'editor', 'picUploader'], function(ui, editContent, message, editor, picUploader) {
		var cont = $('.view-content');
		
		cont.on('ui-sortable-sorted', function() {
			$(window).resize();
			ui.initAll();
		});
		
		var classArray = {"close":"show","public":"show","new":"draft","hidden":"hide"};
		var dropdown = function(){
			ui.dropdown($('.dropdown'),{
				select: function() {
					var selectItem = $(this);
					var dropTgl = selectItem.closest('.dropdown-menu').prev();
					var type = selectItem.data('type');
					selectItem.closest('.dropdown').find('INPUT[name="status"]').val(type);
					dropTgl.find('span').text(selectItem.text());
					dropTgl.find('i').attr("class", "icon-" + classArray[type]);
				}
			});
		};
		
		cont.on('click', '.action-add', function() {
			var post = $(this).hasClass('action-add-post');
			editContent.open({
				getform: $(this).data('url'),
				getformmethod: 'get',
                getformtype: 'json',
				clearform: true,
				loadform: function() {
					if (post) {
						editor($('.redactor-init', this).removeClass('redactor-init').addClass('redactor'));
						picUploader();
						dropdown();
					}
				},
				success: function(res) {
					if (post) {
						if (res.data.url) {
							location.href = res.data.url;
						} else {
							message.ok('Статья сохранена');
						}
					} else {
						cont.html(res.content);
						editContent.close();
						$(window).resize();
						ui.initAll();
					}
				}
			});
			return false;
		});
		
		cont.on('click', '.themes-list .action-edit', function() {
			var id = $(this).closest('.wblock').data('id');
			editContent.open({
				getform: $(this).attr('href'),
				getformdata: {id: id},
				getformmethod: 'get',
                getformtype: 'json',
				success: function(res) {
					cont.html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		cont.on('click', '.action-delete', function() {
			var theme = $(this).hasClass('delete-theme');
			var url = $(this).attr('href') || $(this).data('delurl');
			var id = $(this).closest('.wblock').data(theme? 'id' : 'id');
            message.confirm({
				text: 'Подтвердите удаление ' + (theme? 'темы' : 'статьи') + '.',
				type: 'delete',
				ok: function() {
					$.post(url, {id: id}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							cont.html(res.content);
							$(window).resize();
							ui.initAll();
						}
					}, 'json');
				}
			});
			return false;
		});
		// выбор сегмента
		if ($('.main-tabs').length) {
			var getPosts = function(el) {
				var url = el.data('posturl');
				var segment = el.data('segment');
				var target = el.data('target');
				$.post(url, {s: segment}, function(res) {
					if (res.errors) {
						message.errors(res);
					} else {
						el.addClass('m-changed');
						$(target).find('.white-body').html(res.content);
						$(window).resize();
						ui.initAll();
					}
				}, 'json');
			};
			$("body").on('ui-tabs-change', '.main-tabs', function() {
				var main = $(this);
				var tab = $('.tab-title.m-current', main);
				if ( tab.hasClass('m-changed'))	{
					$('.tab-title').removeClass('m-changed');
					return;
				} else {
					getPosts(tab);
				};
			});
			$('.tab-title').each(function() {
				var tab = $(this);
				getPosts(tab);
			});
		}
		
    });
});