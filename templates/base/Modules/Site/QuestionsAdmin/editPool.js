$(function() {
	require(['ui', 'userForm', 'saveIcon'], function(ui, userForm, saveIcon) {
		
		var qList = $('.question-list');
		var initQ = function() {
			$('.question-item', qList).each(function() {
				if ($(this).data('q-inited')) return;
				$(this).data('q-inited', true);
				var cont = $(this);
				var answer = $('.new-answer', cont);
				var newId = parseInt($('.answer-item:last', cont).data('id')) + 1;
				answer.attr('data-id', newId);
				//$('.add-a', answer).attr('name', $('.add-a', answer).data('name').replace('[]', '[' + newId + ']'));
			});
		};
		initQ();
		var initSort = function() {
			qList.sortable({
				handle: '.drag-drop',
				items: 'li.question-item',
				stop: function(event, sortUi) {
					var questionId = parseInt(sortUi.item.data('id'));
					var oldPosition = parseInt(sortUi.item.data('position'));
					var newPosition = parseInt(sortUi.item.next().data('position'));
					if (oldPosition !== newPosition - 1) {
						if (isNaN(newPosition) || newPosition > oldPosition) {
							newPosition = parseInt(sortUi.item.prev().data('position'));
						}
						$.ajax({
							url: "/questions-admin/questionList/",
							type: "POST",
							data: ({
								question_id: questionId,
								position: newPosition
							}),
							success: function(responseText) {
								qList.html(responseText);
								ui.initAll();
								initSort();
								initQ();
							}
						});
					}
				}
			});
		};
		initSort();
		qList.on('focus', '.add-a', function() {
			$(this).closest('TR').addClass('m-focus');
		}).on('blur', '.add-a', function() {
			if ($(this).val()) $(this).closest('TR').addClass('m-focus');
			else $(this).closest('TR').removeClass('m-focus');
		}).on('click', '.add-btn', function() {
			var cont = $(this).closest('.new-answer');
			var lastAns = $('.answer-item:last', cont.closest('.question-item'));
			var id = parseInt(cont.attr('data-id'));
			var i = parseInt(lastAns.attr('data-i')) + 1;
			var newAns = lastAns.clone().attr('data-id', id).attr('data-i', i);
			$('.a-num', newAns).text(i);
			$('INPUT', newAns).attr('name', $('.add-a', cont).attr('name')).val($('.add-a', cont).val());
			lastAns.after(newAns);
			cont.attr('data-id', id + 1);
			//$('.add-a', cont).attr('name', $('.add-a', cont).data('name').replace('[]', '[' + (id + 1) + ']')).val('');
			return false;
		}).on('click', '.delete-a', function() {
			if (!confirm('Удалить вариант ответа?')) return false;
			var cont = $(this).closest('.question-item');
			$(this).closest('.answer-item').slideUp(function() {
				$(this).remove();
				$('.answer-item', cont).each(function(i) {
					i += 1;
					$(this).attr('data-i', i);
					$('.a-num', this).text(i);
				});
			});
			return false;
		}).on('click', '.delete-q', function() {
			if (!confirm('Удалить вопрос?')) return false;
			var btn = $(this);
			$.ajax({
				url: btn.attr('href'),
				data: {id: btn.data('id')},
				type: 'post',
				dataType: 'json',
				success: function(res){
					if (res.errors){
					} else {
						btn.closest('.question-item').slideUp(function() {
							$(this).remove();
						});
					}
				}
			});
			return false;
		});
		
		$('.action-add A').click(function(){
                    $('.popup-add-question INPUT').val('');
                    $('.popup-add-question').dialog({
			title: 'Введите вопрос'
                    }).dialog('open');
                    return false;
		});
                
                $('.popup-add-question FORM').submit(function(evt){
                    evt.preventDefault();
                    $(this).ajaxSubmit({
                        dataType: 'json',
                        success: function(res){
                            if (res.errors){
                            } else {
                                qList.append(res.content);
                                ui.initAll();
                                initSort();
                                initQ();
                                $('.popup-add-question').dialog('close');
                            }
                        }
                    });
                });

		$('.action-save A').click(function(){
			$('.edit-questions-form').submit();
			return false;
		});
		userForm.init($('.edit-questions-form'), {
			errors: function(err) {
				console.log(err);
			},
			success: function() {
				saveIcon();
			}
		});
		
	});
	
		
});