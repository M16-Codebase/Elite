$(function() {
	require(['ui', 'editContent', 'message', 'imgpreview'], function(ui, editContent, message, imgpreview) {
		
		//видимость тизера во время редактирования
		var classArray = {"1":"show","0":"hide"};
		var dropdown = function(){
			ui.dropdown($('.teaser-show-dropdown'),{
				select: function() {
					var selectItem = $(this);
					var dropTgl = selectItem.closest('.dropdown-menu').prev();
					var type = selectItem.data('type');
					selectItem.closest('.dropdown').find('INPUT[name="active"]').val(type);
					dropTgl.find('span').text(selectItem.text());
					dropTgl.find('i').attr("class", "icon-" + classArray[type]);
				}
			});
		};
		
		//добавить тизер
		$('.actions-panel .action-button.action-add').click(function() {
			editContent.open({
				getform: '/site-teaser/teaserFields/',
				loadform: function() {
					imgpreview();
					dropdown();
					ui.datepicker($('#from'), {
						onClose: function( selectedDate ) {
							$('#to').datepicker( "option", "minDate", selectedDate );
						},
						maxDate: $('#to').val()
					});
					ui.datepicker($('#to'), {
						onClose: function( selectedDate ) {
							$('#from').datepicker( "option", "maxDate", selectedDate );
						},
						minDate: $('#from').val()
					});
				},
				success: function(res) {
					$('.viewport').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			});
			return false;
		});
		
		// редактировать баннер
		$('.viewport').on('click', '.white-block-row .action-edit', function() {
            var teaserId = $(this).closest('.white-block-row').data('id');
			editContent.open({
				getform: '/site-teaser/teaserFields/', 
				getformdata: {
					id: teaserId, 
				},
				errorsText: {
					"active:cant_activate": "Невозможно отобразить тизер, так как истек срок показа"
				},
				loadform: function() {
					imgpreview();
					dropdown();
					ui.datepicker($('#from'), {
						onClose: function( selectedDate ) {
							$('#to').datepicker( "option", "minDate", selectedDate );
						},
						maxDate: $('#to').val()
						
					});
					ui.datepicker($('#to'), {
						onClose: function( selectedDate ) {
							$('#from').datepicker( "option", "maxDate", selectedDate );
						},
						minDate: $('#from').val()
					});
				},
				success: function(res) {
					$('.viewport').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
					customSlidebox();
				}
			});
			return false;
		});
		
		// удалить баннер
		$('.viewport').on('click', '.white-block-row .action-delete', function() {
			var teaserId = $(this).closest('.white-block-row').data('id');
			message.confirm({
				text: 'Подтвердите удаление баннера.',
				type: 'delete',
				ok: function() {
					$.post('/site-teaser/delete/', {id: teaserId}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							$('.viewport').html(res.content);
							$(window).resize();
							ui.initAll();
							customSlidebox();
						}
					}, 'json');
				}
			});
			return false;
		});
		
		//видимость тизера
		$('.viewport').on('click', '.white-block-row .action-visibility', function() {
			var btn = $(this);
            var teaserId = $(this).closest('.white-block-row').data('id');
			var active = $(this).hasClass('action-show')? 0 : 1;
			$.post('/site-teaser/activate/', {
				active: active,
                id: teaserId,
			}, function(res) {
				if (res.errors) {
					if (res.errors.active == 'cant_activate') {
						message.errors("Невозможно отобразить баннер, так как истек срок показа");
					} else {
						message.errors(res);
					}
				} else {
					btn.removeClass('action-show action-hide').addClass(active? 'action-show' : 'action-hide').attr('title', active? 'Показан' : 'Скрыт');
					$('I', btn).removeClass('icon-show icon-hide').addClass(active? 'icon-show' : 'icon-hide');
				}
			}, 'json');
			return false;
		});
		
	});
});