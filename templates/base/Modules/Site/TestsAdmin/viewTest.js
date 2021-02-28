$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		var testId = $(".viewport").data("test-id");
		
		$("body").on("click", ".edit-questions-form INPUT[type=radio]", function(){
			var el = $(this);
			var body = el.data('body');
			if (el.prop('checked')) {
				ui.slidebox.open(body);
			} else {
				ui.slidebox.close(body);
			};
		});
	
		//добавить вопрос
		$('.actions-panel .action-button.action-add').click(function() {
			var tab = $(this).closest('.tab-page');
			var addUrl = tab.data('add-url');
			editContent.open({
				getform: addUrl,
				getformdata: {
					test_id: testId
				},
				loadform: function() {
					if(tab.attr("id") == 'questions'){
						ui.slidebox('.slidebox-1',{
							body: '.slide-body-value',
							open: function() {$(window).resize();},
							close: function() {$(window).resize();}
						});
						ui.slidebox('.slidebox-2',{
							body: '.slide-body-answer',
							open: function() {$(window).resize();},
							close: function() {$(window).resize();}
						});
					}
				},
				getformtype: 'json',
				success: function(res) {
					$('.white-blocks', tab).html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		$('BODY').on('click', '.edit-questions-form .answers-list .add-btn', function() {
			var origin = $(this).closest('.row').siblings('.origin');
			var count = $('.answer').length;
			$("INPUT:eq(0)", origin).attr({"name":"answers["+ (count) +"][answer]"});
			$("INPUT:eq(1)", origin).attr({"name":"answers["+ (count) +"][value]"});
			var newRow = origin.clone().removeClass('a-hidden origin').hide();
			newRow.insertBefore(origin).slideDown(300, function() {
				$(window).resize();
			});
			return false;
		}).on('click', '.edit-questions-form .answers-list .action-delete', function() {
			var delItem = $(this).closest('.answer');
			message.confirm({
				text: 'Подтвердите удаление ответа.',
				ok: function() {
					delItem.slideUp(300, function(){
						delItem.remove();
					});
				}
			});
			return false;
		});
//		
//		// редактировать вопрос
		$('#questions .viewport').on('click', '.white-block-row .action-edit', function() {
			var tab = $(this).closest('.tab-page');
			var questionId = $(this).closest(".white-block-row").data("id");
			editContent.open({
				getform: '/tests-admin/questionFields/',
				getformdata: {
					id: questionId,
					test_id: testId
				},
				getformtype: 'json',
				loadform: function() {
					if(tab.attr("id") == 'questions'){
						ui.slidebox('.slidebox-1',{
							body: '.slide-body-value',
							open: function() {$(window).resize();},
							close: function() {$(window).resize();}
						});
						ui.slidebox('.slidebox-2',{
							body: '.slide-body-answer',
							open: function() {$(window).resize();},
							close: function() {$(window).resize();}
						});
					}
				},
				success: function(res) {
					$('.white-blocks', tab).html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		// удалить вопрос
		$('.viewport').on('click', '.white-block-row .action-delete', function() {
			var tab = $(this).closest('.tab-page');
			var questionId = $(this).closest('.white-block-row').data('id');
			var delUrl = tab.data('del-url');
			message.confirm({
				text: 'Подтвердите удаление вопроса.',
				type: 'delete',
				ok: function() {
					$.post(delUrl, {test_id: testId, id: questionId}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							$('.white-blocks', tab).html(res.content);
							$(window).resize();
							ui.initAll();
						}
					}, 'json');
				}
			});
			return false;
		});	
		
		
		//РЕЗУЛЬТАТЫ
	});
});