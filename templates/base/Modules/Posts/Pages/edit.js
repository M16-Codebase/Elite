$(function() {
	require(['ui', 'message', 'editor', 'picUploader'], function(ui, message, editor, picUploader) {
		
		/*variables*/
		var saveButton=$('.actions-panel .action-save'),
			delButton=$('.actions-panel .action-delete');
			
		$('.tag-edit').tagEditor({});
		
		saveButton.bind('click', function() {
			$('#edit_post_form').submit();
			return false;
		});
		$('.actions-panel').on('click', '.action-delete',function() {
			var btn = $(this);
			message.confirm({
				text: 'Подтвердите удаление текста.',
				type: 'delete',
				ok: function() {
					$.ajax({
						url: btn.data('delurl'),
						type: 'post',
						data: {
							id: btn.data('id')
						},
						dataType: 'json',
						success: function(res){
							if (res.errors === null){
								document.location.replace(res.data.url);
							}
						}
					});
				}
			});
			return false;
		});
		
        var scrollBlock = $('.content-scroll .viewport .list');
        scrollBlock.on('submit', '#edit_post_form', function(evt){
			var delName = $('.actions-panel .action-save').data('savename');
            evt.preventDefault();
            $(this).ajaxSubmit({
                dataType: 'json',
                success: function (res) {
                    if (res.errors){
						message.errors({
							errors: res.errors,
							errorsText: {
								'title:empty': 'Не указан заголовок.',
								'text:empty': 'Не заполнен текст.',
								'text:count_symbols': 'Минимальная длина текста — 10 символов.'
							}
						});
                    } else {
						scrollBlock.html(res.content);
						$('.tag-edit').tagEditor({});
						editor($('.redactor-init').removeClass('redactor-init').addClass('redactor'));
						picUploader('.img-uploader-gallery');
						$(window).resize();
						message.ok(delName + ' сохранена.');
						dropdown();
					}
                }
            });
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
		dropdown();
		
	});
});