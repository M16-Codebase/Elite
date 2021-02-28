{*
    Все поля пользователя:
    'id', 'role', 'pass_hash', 'name', 'status', 'email', 'auth', 'email_valid', 
    'region_id', 'person_type', 'phone', 'requisites', 'reg_date', 'company_name', 
    'region', 'reg_timestamp','inn','surname','referer','referal_number'
    Адреса отдельно $user->getAddresses() - теперь это массив вида array('id_адреса' => 'текст')
    Поля которые может редактировать пользователь:
    'name', 'email', 'person_type', 'phone', 'requisites', 'company_name','inn','surname', 'referer'(только при создании)
    'address[$address_id]' - уже записанные адреса
    'new_addresses[]' - только что добавленные адреса
*}
	
<form class="auth-form register" method="POST" autocomplete="off">
	<div class="header-yellow">
		<a href="/" class="logo">
			
		</a>
	</div>
	<div class="auth-body">
		<h3>Регистрация</h3>
		<div class="field">
			<div class="f-title">E-mail</div>
			<div class="f-input"><input type="text" name="email" /></div>
		</div>
		<div class="field">
			<div class="f-title">Пароль</div>
			<div class="f-input"><input type="password" name="pass" /></div>
		</div>
		<div class="field">
			<div class="f-title">Повтор пароля</div>
			<div class="f-input"><input type="password" name="pass2" /></div>
		</div>
		<div class="field">
			<div class="f-title">Фамилия</div>
			<div class="f-input"><input type="text" name="surname" /></div>
		</div>
		<div class="field">
			<div class="f-title">Имя</div>
			<div class="f-input"><input type="text" name="name" /></div>
		</div>
		<div class="field">
			<div class="f-title">Телефон</div>
			<div class="f-input"><input type="text" name="phone" /></div>
		</div>
		{if !empty($errors)}
			<div class="errors">
				<strong>Ошибка при регистрации</strong>
				{$errors|var_dump}
			</div>
		{/if}
		<div class="buttons">
			<button class="btn btn-sq-blue">Зарегистрироваться</button>
		</div>
	</div>
</form>