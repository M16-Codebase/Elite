$(function(){
	require(['ui', 'editContent', 'message', 'imgpreview'], function(ui, editContent, message, imgpreview) {
		var usersList = $('.users-list');
		var filter = $('.aside-filter FORM');
		
		var randomPassword = function() {
			var pass_length = 8;
			var allow = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			var i = 1;
			var ret = '';
			while (i <= pass_length) {
				var max  = allow.length - 1;
				var num  = Math.floor(Math.random() * (max + 1));
				var temp = allow.substr(num, 1);
				ret  = ret + temp;
				i++;
			}
			return ret;
		};

		var deleteUser = function(id) {
			if (!id) return false;
			message.confirm({
				text: 'Подтвердите удаление пользователя.',
				type: 'delete',
				ok: function() {
					$.post('/users-edit/deleteUser/', {id: id}, function(res) {
						if (res.errors) {
							message.errors(res);
						} else {
							if (res.content) {
								usersList.html(res.content);
							}
							editContent.close();
							$(window).resize();
							ui.initAll();
						}
					}, 'json').error(function(err) {
						message.serverErrors(err);
					});
				}
			});
		};

		// форма редактирования
		var initScripts = function(form) {
			ui.initAll();
			
			var userId = $('[name="id"]', form).val();
			
			// удаление
			$('.content-top .action-delete', form).click(function() {
				deleteUser(userId);
				return false;
			});

			// смена типа
			var checkType = function(type) {
				if (!type) type = $('.user-type-select', form).val();
				switch (type) {
					case 'man':
						$('.show-for-not-man, .show-for-fiz, .show-for-org', form).addClass('a-hidden');
						$('.show-for-man', form).removeClass('a-hidden');
						break;
					case 'fiz':
						$('.show-for-man, .show-for-org', form).addClass('a-hidden');
						$('.show-for-not-man, .show-for-fiz', form).removeClass('a-hidden');
						break;
					case 'org':
						$('.show-for-man, .show-for-fiz', form).addClass('a-hidden');
						$('.show-for-not-man, .show-for-org', form).removeClass('a-hidden');
						break;
				}
				$(window).resize();
			};
			$('.user-type-select', form).change(function() {
				checkType($(this).val());
			});

			// удаление фото
			$('.delete-photo', form).click(function() {
				var btn = $(this);
				var block = btn.closest('.white-block-row');
				message.confirm({
					text: 'Подтвердите удаление фотографии.',
					type: 'delete',
					ok: function() {
						$.post(btn.attr('href'), {id: btn.data('user_id')}, function(res) {
							if (res.errors) {
								message.errors(res);
							} else {
								message.ok('Фотография удалена');
								$('.img-preview-body', block).html(' ').closest('.row').addClass('a-hidden');
								btn.fadeOut(0);
								block.find('.add-btn SPAN').text('Добавить изображение');
								block.find('.add-btn I').attr('class','icon-add');
								$('INPUT[type=file]', block).val('');
							}
						}, 'json').error(function(err) {
							message.serverErrors(err);
						});
					}
				});
				return false;
			});
			
			// смена роли
			$('.user-role-select', form).change(function() {
				var type = $(this).closest('FORM').find('.user-type-select');
				var role = $(this).val();
				if (role !== 'User') {
					$('.admin', type).attr('disabled', false).attr('selected', true).siblings().attr('disabled', true);
				} else {
					$('.admin', type).attr('selected', false).attr('disables', true).siblings().attr('disabled', false);
					if (type.val() === 'man'){
						$('.admin', type).siblings().first().attr('selected', true);
					}
				}
			}).change();
			
			// список адресов
			$('.add-address', form).each(function() {
				var enumList = $(this);
				var enumListProps = $('.org-values', enumList);
				var enumListOrigin = $('.origin', enumList);
				var enumListAdd = $('.add-value', enumList);			
				var addToEnum = function() {
					if (!$('INPUT', enumListAdd).val()) return false;
					var origin = enumListOrigin.clone();
					$('INPUT', origin).val($('INPUT', enumListAdd).val()).attr('name', $('INPUT', enumListAdd).attr('name'));
					enumListProps.append(origin.removeClass('origin'));
					$('INPUT', enumListAdd).val('').blur();
					$(window).resize();
					return false;
				};
				$('.action-add', enumListAdd).click(addToEnum);
				$('INPUT', enumListAdd).keydown(function(e) {
					if (e.keyCode === 13) {
						addToEnum();
						return false;
					}		
				});
				enumListProps.on('click', '.action-delete', function() {
					var btn = $(this);
					message.confirm({
						text: 'Подтвердите удаление адреса.',
						type: 'delete',
						ok: function() {
							btn.closest('.org-item').fadeOut(function() {
								$(this).remove();
								$(window).resize();
							});
						}
					});
				});
			});
			
			// генерация пароля
			$('.make-random', form).click(function(){
				var pass = randomPassword();
				var field = $(this).closest('.wblock');
				$('INPUT[name="pass"]', field).val(pass);
				$('INPUT[name="pass2"]', field.next()).val(pass);
				return false;
			});
			
			checkType();
			
		};
		
		var errorsText = {
			'email:empty': 'Укажите e-mail.',
			'email:incorrect_format': 'Неверный формат e-mail.',
			'email:already_exists': 'Такой e-mail уже зарегистрирован.',
			'pass:empty': 'Укажите пароль.',
			'pass:count_symbols': 'Пароль слишком короткий',
			'pass2:not_same': 'Пароли не совпадают.',
			'name:empty': 'Укажите имя.',
			'surname:empty': 'Укажите фамилию.',
			'phone:empty': 'Укажите телефон.',
			'ogrn:empty': 'Укажите код ОГРН.',
			'company_name:empty': 'Укажите название компании.'
		};
		
		// добавление
		$('.actions-panel .action-add').click(function() {
			editContent.open({
				form: '.creat-user-form',
				clearform: true,
				loadform: function() {
					$('SELECT', this).each(function() {
						$('OPTION.default', this).prop('selected', true);
					});
					initScripts(this);
					imgpreview($('.img-preview', this));
					$(window).resize();
					ui.initAll();
				},
				success: function(res) {
					if (res.content) {
						usersList.html(res.content);
					}
					editContent.close();
					$(window).resize();
					ui.initAll();
				},
				errorsText: errorsText
			});
			return false;
		});
		
		// редактирование
		usersList.on('click', '.action-edit', function() {
			var row = $(this).closest('.wblock');
			editContent.open({
				getform: '/users-edit/editUserFields/',
				getformdata: {user_id: row.data('user_id')},
				loadform: function() {
					initScripts(this);
					imgpreview($('.img-preview', this));
				},
				beforeSubmit: function() {
					var form = $(this);
					if (form.hasClass('confirmed')) {
						form.removeClass('confirmed');
						return true;
					}
					message.confirm({
						text: 'Подтвердите изменение пользователя.',
						ok: function() {
							form.addClass('confirmed');
							form.submit();
						}
					});
					return false;
				},
				afterSubmit: function() {
					$(this).removeClass('confirmed');
				},
				success: function(res) {
					if (res.content) {
						usersList.html(res.content);
					}
					editContent.close();
					$(window).resize();
					ui.initAll();
				},
				errorsText: errorsText
			});
			return false;
		});
		
		
		// удаление
		usersList.on('click', '.action-delete', function() {
			deleteUser($(this).closest('.wblock').data('user_id'));
			return false;
		});
		
		// фильтр
		ui.form(filter, {
			method: 'get',
			success: function(res) {
				history.replaceState({}, '', '?' + $(this).formSerialize());
				if (res.content) {
					usersList.html(res.content);
				}
				editContent.close();
				$(window).resize();
				ui.initAll();
			},
			errors: function(errors) {
				message.errors({errors: errors});
			},
			serverError: function(err) {
				message.serverErrors(err);
			}
		});
		
		// сортировка
		usersList.on('click', '.white-header .sort-link', function() {
			var sort = $(this).data('sort');
			var val = $(this).data('val');
			$('.input-sort', filter).attr('name', sort).val(val);
			filter.submit();
			return false;
		});
			
	});
});