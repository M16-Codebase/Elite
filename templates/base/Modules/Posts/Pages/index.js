$(function() {
	require(['ui', 'editContent', 'editor', 'message'], function(ui, editContent, editor, message) {
		
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
		
		$('.actions-panel .action-add').click(function() {
			var segmentId = $('.tab-title.m-current').data('segment');
            editContent.open({
                getform: $(this).attr('href'),
                getformtype: 'json',
				getformdata: {
					segment_id: segmentId, 
				},
                clearform: true,
				loadform: function(){
					editor($('.redactor-init', this).removeClass('redactor-init').addClass('redactor').blur());
					dropdown();
				},
                success: function(res) {
					if (!res.errors){
						location.replace(res.data.url);
					} else {
						message.errors(res);
					}
                }
            });
            return false;
		});
		
		// удаление
		$('.white-body').on('click', '.action-delete', function(){
			var id = $(this).closest('.white-block-row').data('id');
			var delUrl = $(this).data('delurl');
			var delName = $(this).data('delname');
			message.confirm({
				text: 'Подтвердите удаление ' + delName + '.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url:  delUrl,
						type: 'post',
						data: {id: id},
						dataType: 'json',
						success: function(res){
							if (!res.errors){
								$('.white-body').html(res.content);
								$(window).resize();
							} else {
								message.errors(res);
							}
						}
					});
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