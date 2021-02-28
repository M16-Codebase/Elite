$(function() {
	require(['ui', 'editContent', 'message'], function(ui, editContent, message) {
		
		//добавить фразу
		$('.actions-panel .action-button.action-add').click(function() {
			editContent.open({
				form: '.create-phrase-form',
				success: function(res) {
					$('.list').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		// удалить фразу
		$('.viewport').on('click', '.white-block-row .action-delete', function() {
			var id = $(this).closest('.white-block-row').data('id');
			message.confirm({
				text: 'Подтвердите удаление поисковой фразы.',
				type: 'delete',
				ok: function() {
					$.post('/site-search/deletePhrase/', {id: id}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							$('.list').html(res.content);
							$(window).resize();
							ui.initAll();
						}
					}, 'json');
				}
			});
			return false;
		});
		
		// редактировать фразу
		$('.viewport').on('click', '.white-block-row .action-edit', function() {
            var id = $(this).closest('.white-block-row').data('id');
			editContent.open({
				getform: '/site-search/phraseFields/',
				getformdata: {
					id: id, 
				},
				getformtype: 'json',
				success: function(res) {
					$('.list').html(res.content);
					editContent.close();
					$(window).resize();
					ui.initAll();
				}
			});
			return false;
		});
		
		var filter = $('.order-logs');
		var list = $('.list');
		// фильтр
		ui.form(filter, {
			method: 'get',
			success: function(res) {
				history.replaceState({}, '', '?' + filter.formSerialize());
				if (res.content) {
					list.html(res.content);
					$(window).resize();
					ui.initAll();
				}
			},
			errors: function(errors) {
				message.errors({errors: errors});
			},
			serverError: function(err) {
				message.serverErrors(err);
			}
		});
		// сортировка
		list.on('click', '.white-header .sort-link', function() {
			var sort = $(this).data('sort');
			var val = $(this).data('val');
			$('.input-sort', filter).attr('name', sort).val(val);
			filter.submit();
			return false;
		});
//		$('.view-content .action-add').on('click', function() {
//			editContent({
//				form: '.create-phrase-form'
//			});
//			return false;
//		});
//		
//		
//		
//		
//		/*variables*/
//		var delPhrase=$(".white-block-row .action-delete");
//		var addPhraseButton=$(".view-content .action-add");
//		var editPhraseButton=$(".editPhrase");
//		var createForm=$(".create-phrase-form");
//		var createSubmit=$(".action-save", createForm);	
//		var editForm=$(".edit-phrase-form");
//		var editFormOldPhrase=$("input[name='old_phrase']", editForm);
//		var editFormPhrase=$("input[name='phrase']", editForm);
//		var editFormUrl=$("input[name='url']", editForm);
//		var editSubmit=$(".action-save", editForm);
//		var ajaxOptions={
//			dataType: 'json',
//			success: function(result){
//				console.log(result);
//				if (result.url){
//					window.location.href = result.url;
//				} else if (result.error) {
//					alert('Необходимо заполнить все поля');
//				}
//			}
//		};
//
//		/*common functions*/
//		function showForm(form) {
//			form.show().siblings().hide();
//			$('.main-content-inner').addClass('m-edit');		
//		}
//
//		/*events*/
//
//		//edit search phrase
//		editPhraseButton.click(function() {
//			var target=$(this);
//			var parent=target.closest(".white-block-row");
//			var phrase = $('.data_phrase', parent).text();
//			var phrase_url = $('.data_url', parent).text();
//			editFormPhrase.val(phrase);
//			editFormOldPhrase.val(phrase);
//			editFormUrl.val(phrase_url);
//			showForm(editForm);			
//		});
//
//		//edit form submit
//		editSubmit.click(function(){
//			editForm.ajaxSubmit(ajaxOptions);
//		});
//
//		createSubmit.click(function() {
//			createForm.ajaxSubmit(ajaxOptions);
//		});
//
//		//delete search phrase
//		delPhrase.click(function(){
//			if (confirm('Удалить поисковую фразу?')){
//				var parent = $(this).closest('.white-block-row');
//				var phrase = $('.data_phrase', parent).text();
//				$.getJSON('/site-search/del/', {phrase: phrase}, function(result){
//					if (result.url){
//						window.location.href = result.url;
//					} else{
//						alert(result.error);
//					}
//				});
//			}
//			return false;
//		});
		
	});
});