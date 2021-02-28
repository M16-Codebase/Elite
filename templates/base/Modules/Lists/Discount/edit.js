$(function(){
	require(['message'], function(message) {
		
		$('FORM.saveDiscount').ajaxForm({
			dataType: 'json',
			success: function(res){
				if (res.errors){
					message.errors(res);
				} else {
					message.ok('Изменения сохранены.');
				}
			}
		});
		
		
		// номера товаров
		var initNemArea = function() {
			$('.num-area').each(function() {
				var cont = $(this);
				var input = cont.next('INPUT');
				var nNumReg = /\d+/g;
				var saveNum = function() {
					var nums = _.map(cont.tagit('tags'), function(tag) {
						return $(tag.element).attr('tagvalue') || $(tag.element).text();
					});
					input.val(nums.join(','));
				};
				var setError = function(el, text) {
					var timer = 0;
					$(el).addClass('m-error').attr('title', text);
					timer = setTimeout(function() {
						$(el).fadeOut(function() {
							$('.tagit-close', el).click();
						});
					}, 3000);
					$('.tagit-close', el).click(function() {
						clearTimeout(timer);
					});
				};
				var preAdded = {};
				$('LI.tagit-choice', cont).each(function() {
					preAdded[$(this).attr('tagvalue')] = {
						title: $(this).attr('title'),
						text: $(this).text()
					};
				});
				cont.tagit({
					highlightOnExistColor: '#ea3131',
					beforeAdded: function(code, value) {
						var match = code.match(nNumReg);
						return {
							value: code,
							label: code
						};
					},
					tagsChanged: function(code, action, el) {
						var match = code.match(nNumReg);
						if (match) {
							switch(action) {
								case 'added':
									$.post('/catalog-item/checkIssetItemById/', {id: code}, function(res) {
										if (res.error) {
											setError(el, 'Товар не найден');								
										} else {
											$(el).attr('title', res.title).attr('tagvalue', res.id);
											saveNum();
										}
									}, 'json').fail(function() {
										setError(el, 'Ошибка сервера');
									});
									break;
								case 'popped':
									saveNum();
									break;
							}
						} else {
							setError(el, 'Неверный формат');
						}
					}
				});
				for (var id in preAdded) {
					$('.tagit-choice[tagvalue="' + preAdded[id].text + '"]', cont).attr('tagvalue', id).attr('title', preAdded[id].title);
				}
				saveNum();
			});
		};
		initNemArea();
		
	});	
});