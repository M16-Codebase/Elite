{?$order_config = $site_config->get(null, 'order')}
{if !empty($order_config)}
	{?$bonus_enable = $order_config.properties.bonus_enable.value}
{else}
	{?$bonus_enable = 0}
{/if}

{capture assign=aside_filter name=aside_filter}
	<section class="aside-filter">
		<form method="GET" class="user-form items-filter" action="/users-edit/usersList/" data-ignoreempty="1">
			<input type="hidden" name="type" value="{$current_person_type}" />
			<input type="hidden" name="sort" class="input-sort" />
			<div class="field">
				<div class="f-title">E-mail</div>
				<div class="f-input">
					<input type="text" name="email" />
				</div>
			</div>
			{if $current_person_type != 'man'}
				<div class="field">
					<div class="f-title">Фамилия</div>
					<div class="f-input">
						<input type="text" name="surname" />
					</div>
				</div>
			{/if}
			{if $current_person_type == 'org'}
				<div class="field">
					<div class="f-title">Наименование организации</div>
					<div class="f-input">
						<input type="text" name="company_name" />
					</div>
				</div>
				<div class="field">
					<div class="f-title">ИНН</div>
					<div class="f-input">
						<input type="text" name="inn" />
					</div>
				</div>
			{/if}
			<div class="field">
				<div class="f-title">Дата регистрации</div>
				<div class="f-input between">
					<input type="text" name="date_min" mask="99.99.9999" class="datepicker a-left">
					<input type="text" name="date_max" mask="99.99.9999" class="datepicker a-right">
				—
				</div>
			</div>
			<div class="buttons">
				<button class="submit btn btn-main a-block">Показать</button>
				<div class="link-cont">
					<span class="clear-form a-link small-descr">Сбросить фильтр</span>
				</div>
			</div>
		</form>
	</section>
{/capture}


<div class="content-top">
	{capture assign="person_type_title"}
		{if $current_person_type == 'fiz'}Физические лица{elseif $current_person_type == 'org'}Юридические лица{elseif $current_person_type == 'man'}Администраторы{else}Пользователи{/if}	
	{/capture}
	{?$pageTitle =  (!empty($person_type_title) ? $person_type_title . ' — ' : '') . (!empty($confTitle) ? $confTitle : '')}
	<h1>{$person_type_title}</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			buttons = array(
				'add' => ($account->isPermission('users-edit', 'createUser')? array(
					'text' => 'Создать'
				) : 0)
			)
		}
	</div>
</div>

<div class="content-scroll">
	<div class="viewport">
		<div class="white-blocks users-list">
			{include file="Modules/Profile/EditUsers/usersList.tpl"}
		</div>
		{include file="Admin/components/paging.tpl"}
	</div>
</div>

{include file="/Modules/Profile/EditUsers/create_user.tpl" assign=create_user}
{capture assign=editBlock name=editBlock}
	{$create_user|html}
{/capture}