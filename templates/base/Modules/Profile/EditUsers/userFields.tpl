<div class="wblock white-block-row">
	<div class="w3">
		<strong>E-mail</strong>
	</div>
	<div class="w9">
		<input type="text" name="email" class="user_name"{if empty($allow_edit)} disabled{/if} />
	</div>
</div>

<div class="wblock white-block-row">
	<div class="w3">
		<strong>Роль</strong>
	</div>
	<div class="w9">
		<input type="hidden" name="person_type" class="user-type-select" value="man" />
		{if !empty($create_user)}
			<select name="role" class="user-role-select"{if empty($allow_edit) || $accountType != 'SuperAdmin'} disabled{/if}>
				{foreach from=$roles item=role}
					{if $accountType != 'SuperAdmin' && $role.key == 'SuperAdmin'}
					{elseif $role.key != 'Guest'}
						<option value="{$role.key}"{if ($current_person_type != 'man' && $role.key == 'User') || ($current_person_type == 'man' && $role.key == 'SuperAdmin')} class="default"{/if}>
							{$role.title}
						</option>
					{/if}
				{/foreach}
			</select>
		{else}
			<select name="role" class="user-role-select"{if empty($allow_edit) || $accountType != 'SuperAdmin'} disabled{/if}>
				{foreach from=$roles item=role}
					{if $accountType != 'SuperAdmin' && $role.key == 'SuperAdmin'}
					{elseif $role.key != 'Guest'}
						<option value="{$role.key}">{$role.title}</option>
					{/if}
				{/foreach}
			</select>
		{/if}
	</div>
</div>
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Статус</strong>
	</div>
	<div class="w9">
		<select name="status">
			<option value="active"{if $current_person_type == "man"} class="default"{/if}>Активный</option>
			<option value="banned">Забаненный</option>
			<option value="deleted">Удаленный</option>
		</select>
	</div>
</div>
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Пароль</strong>
	</div>
	<div class="w9">
		<input type="text" name="pass" />
		<div>
			<span class="make-random a-link small-descr">сгенерировать</span>
		</div>
	</div>
</div>
{if !empty($create_user)}
	<div class="wblock white-block-row">
		<div class="w3">
			<strong>Подтверждение пароля</strong>
		</div>
		<div class="w9">
			<input type="text" name="pass2" />
		</div>
	</div>
{/if}
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Фамилия</strong>
	</div>
	<div class="w9">
		<input type="text" name="surname" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Имя</strong>
	</div>
	<div class="w9">
		<input type="text" name="name" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Отчество</strong>
	</div>
	<div class="w9">
		<input type="text" name="patronymic" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row">
	<div class="prop-item w12 img-preview">
		<a href="" class="fancybox row-image origin">
			<div class="preloader"><div></div></div>
			<img src="">
		</a>
		<div class="prop-title h4">Фотография</div>
		<div class="row{if empty($user.image)} a-hidden{/if}">
			<div class="w11 img-preview-body">
				{if !empty($user.image)}
					<a href="{$user.image->getUrl()}" class="fancybox row-image">
						<img src="{$user.image->getUrl(51,51,true)}">
					</a>
				{/if}
			</div>
			<a href="/users-edit/deletePhoto/" class="action-button w1{if empty($user.image)} a-hidden{/if} delete-photo" data-user_id="{if !empty($user)}{$user.id}{/if}">
				<i class="icon-prop-delete"></i>
			</a>
		</div>
		<div class="add-row row m-fullwidth">
			<label for="change-image{if !empty($create_user)}-create{/if}" class="change-img">
			<div class="add-object add-btn w3">
				<i class="icon-{if !empty($user.image)}replace{else}add{/if}"></i> <span class="small-descr">{if !empty($user.image)}Заменить{else}Добавить{/if} изображение</span>
			</div>
			</label>
			<input type="file" name="photo" class="hidden-input" id="change-image{if !empty($create_user)}-create{/if}"/>
		</div>
	</div>
</div>
{*<div class="wblock white-block-row">
	<div class="w3">
		<strong>Фотография</strong>
	</div>
	<div class="w8">
		{if !empty($user.image)}
			<a href="{$user.image->getUrl()}" class="user-image fancybox a-left">
				<img src="{$user.image->getUrl(51,51,true)}" />
			</a>
		{/if}
		<input type="file" name="photo">
	</div>
	{if !empty($user.image)}
		<a href="/users-edit/deletePhoto/" class="action-button action-delete delete-photo w1" data-user_id="{$user.id}">
			<i class="icon-delete"></i>
		</a>
	{else}<div class="w1"></div>{/if}
</div>*}
<div class="wblock white-block-row show-for-not-man">
	<div class="w3">
		<strong>Денежный баланс</strong>
	</div>
	<div class="w9">
		<input type="text" name="money_balance"{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-man">
	<div class="w3">
		<strong>Должность</strong>
	</div>
	<div class="w9">
		<input type="text" name="occupation" />
	</div>
</div>
<div class="wblock white-block-row">
	<div class="w3">
		<strong>Телефон</strong>
	</div>
	<div class="w9">
		<input type="text" name="phone" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-man">
	<div class="w3">
		<strong>Мобильный телефон</strong>
	</div>
	<div class="w9">
		<input type="text" name="mobile_phone" />
	</div>
</div>
<div class="wblock white-block-row show-for-man">
	<div class="w3">
		<strong>Skype</strong>
	</div>
	<div class="w9">
		<input type="text" name="skype" />
	</div>
</div>
<div class="wblock white-block-row show-for-man">
	<div class="w3">
		<strong>ICQ</strong>
	</div>
	<div class="w9">
		<input type="text" name="icq" />
	</div>
</div>
<div class="wblock white-block-row show-for-man">
	<div class="w3">
		<strong>Показывать на странице контактов</strong>
	</div>
	<div class="w9">
		<input type="checkbox" name="show_in_contacts" value="1"/>
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Название компании</strong>
	</div>
	<div class="w9">
		<input type="text" name="company_name" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>ИНН</strong>
	</div>
	<div class="w9">
		<input type="text" name="inn" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>ОГРН</strong>
	</div>
	<div class="w9">
		<input type="text" name="ogrn" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>КПП</strong>
	</div>
	<div class="w9">
		<input type="text" name="kpp" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>ОКПО</strong>
	</div>
	<div class="w9">
		<input type="text" name="okpo" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Юридический адрес</strong>
	</div>
	<div class="w9">
		<input type="text" name="jure_address" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Адрес для доставки документов</strong>
	</div>
	<div class="w9">
		<input type="text" name="document_address" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Телефон организации</strong>
	</div>
	<div class="w9">
		<input type="text" name="organisation_phone" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Факс организации</strong>
	</div>
	<div class="w9">
		<input type="text" name="organisation_fax" class=""{if empty($allow_edit)} disabled{/if} />
	</div>
</div>
<div class="wblock white-block-row show-for-org">
	<div class="w3">
		<strong>Реквизиты</strong>
	</div>
	<div class="w9">
		<textarea name="requisites"{if empty($allow_edit)} disabled{/if}></textarea>
	</div>
</div>
<div class="wblock white-block-row show-for-not-man">
	<div class="w3">
		<strong>Бонус</strong>
	</div>
	<div class="w9">
		<input type="text" name="bonus" class="bonus-field" data-mask="9?99999999" />
	</div>
</div>
<div class="wblock show-for-not-man">
	<div class="white-block-row">
		<div class="w12">
			<strong>Адреса</strong>
		</div>
	</div>
	<div class="white-inner-cont add-address">
		<div class="white-block-row org-item origin">
			<div class="w11">
				<input type="text" />
			</div>
			<div class="action-button action-delete w1" title="Удалить">
				<i class="icon-prop-delete"></i>
			</div>
		</div>
		<div class="white-body org-values">
			{if !empty($addresses)}
				{foreach from=$addresses item=adr key=adr_id}
					<div class="white-block-row org-item">
						<div class="w11">
							<input type="text" name="address[{$adr_id}]" value="{$adr}" />
						</div>
						<div class="action-button action-delete w1" title="Удалить">
							<i class="icon-prop-delete"></i>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
		<div class="white-block-row add-value">
			<div class="w11">
				<input type="text" name="new_addresses[]" placeholder="Добавить адрес" />
			</div>
			<div class="action-button action-add w1" title="Добавить">
				<i class="icon-add"></i>
			</div>
		</div>
	</div>
</div>