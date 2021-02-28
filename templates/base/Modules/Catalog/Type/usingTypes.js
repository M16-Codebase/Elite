$(function() {
	require(['ui', 'popupAlert'], function(ui, popupAlert) {
		
		var popupUsingPreview = function(text, color) {
			var circle = $('.popup-edit-property .using-circle');
			if (text) circle.text(text);
			else circle.text($('.popup-edit-property .short-name-input').val());
			if (color) circle.css({borderColor: color, color: color});
			else circle.css({borderColor: $('.popup-edit-property .color-input').val(), color: $('.popup-edit-property .color-input').val()});
		};

		$('.actions-panel .action-add A').bind('click', function() {
			$.ajax({
				url: '/catalog-type/usingTypeFields/',
				dataType: 'json',
				success: function(res){
					if (res.errors === null){
						$('.popup-edit-property FORM').html(res.content);
						popupUsingPreview();
						$('.popup-edit-property').dialog({
							title: 'Добавить тип применения'
						}).dialog('open');
					}
				}
			});
			return false;
		});

		$('.popup-edit-property').on('change', '.short-name-input', function(){
			if ($(this).val().length > 2) $(this).val($(this).val().substr(0, 2));
			popupUsingPreview();
		});

		$('.popup-edit-property').on('click', '.color-select-btn:not(.disabled)', function(){
			var input = $(this).parents('td').find('.color-input');
			input.val($(this).data('color'));
			$(this).addClass('m-current').siblings().removeClass('m-current');
			popupUsingPreview();
			return false;
		});

		$('.using-types-list').on('click', '.delete-property', function(){
			if (!confirm('Удалить применение?')) return false;
			$.ajax({
				url: $(this).attr('href'),
				type: 'post',
				data: {id: $(this).data('id')},
				dataType: 'json',
				success: function(res){
					if (res.errors === null){
						$('.using-types-list').html(res.content);
					}
				}
			});
			return false;
		});

		$('.using-types-list').on('click', '.edit-property', function(){			
			$.ajax({
				url: '/catalog-type/usingTypeFields/',
				type: 'post',
				data: {id: $(this).data('id')},
				dataType: 'json',
				success: function(res){
					if (res.errors === null){
						$('.popup-edit-property FORM').html(res.content);
						popupUsingPreview();
						$('.popup-edit-property').dialog({
							title: 'Редактировать тип применения'
						}).dialog('open');
					}
				}
			});
			return false;
		});

		$('.popup-edit-property FORM').submit(function(evt){
			$(this).ajaxSubmit({
				dataType: 'json',
				success: function(res){
					if (!res.errors){
						$('.using-types-list').html(res.content);
						$('.popup-edit-property').dialog('close');
						ui.initAll();
					} else {
						var errors = [];
						if (res.errors.title) errors.push('Введите полное название.');
						if (res.errors.short_name) errors.push('Введите сокращение');
						if (res.errors.color) errors.push('Укажите цвет.');
						popupAlert.error({
							text: 'Не удалось сохранить применение.',
							errors: errors
						});
					}
				}
			});
			return false;
		});

		var initSort = function() {
			$('ul.using-types-list').sortable({
				handle: '.drag-drop',
				items: 'li.using-type-item',
				stop: function(event, sortUi) {
					var propertyId = parseInt(sortUi.item.data('id'));
					var oldPosition = parseInt(sortUi.item.data('position'));
					var newPosition = parseInt(sortUi.item.next().data('position'));
					if (oldPosition === newPosition - 1) return;
					if (isNaN(newPosition) && oldPosition > parseInt(sortUi.item.prev().data('position'))) return;
					if (isNaN(newPosition) || newPosition > oldPosition) {
						newPosition = parseInt(sortUi.item.prev().data('position'));
					}
					$.ajax({
						url: "/catalog-type/usingTypesList/",
						type: "POST",
						data: ({
							id: propertyId,
							position: newPosition
						}),
						success: function(responseText) {
							$('ul.using-types-list').html(responseText);
							ui.initAll();
						}
					});
				}
			});
		};
		initSort();
		
	});
});