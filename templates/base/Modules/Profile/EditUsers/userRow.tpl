{if !isset($bonus_enable)}
	{?$order_config = $site_config->get(null, 'order')}
	{if !empty($order_config)}
		{?$bonus_enable = $order_config.properties.bonus_enable.value}
	{else}
		{?$bonus_enable = 0}
	{/if}
{/if}
<div class="wblock user-{$user.id} white-block-row" data-user_id="{$user.id}">
	<div class="w1">
		{$user.id}
	</div>
	<div class="w2 m-border">
		{$user.reg_date|date_format_lang:'%d.%m.%Y':'ru'}
	</div>
	{if $current_person_type == 'man'}
		<div class="w3 m-border">
			<a href="mailto:{$user.email}" title="{$user.email}">{$user.email}</a>
		</div>
		<div class="w3 m-border">
			{if $user.role == 'Unknown'}Не назначена{else}{$user->getRoleTitle()}{/if}
		</div>
	{elseif $current_person_type == 'fiz'}
		<div class="w{if $bonus_enable}4{else}5{/if} m-border">
			<strong>{$user.surname} {$user.name}</strong>
		</div>
		{if $bonus_enable}
			<div class="w1 m-border">
				{if !empty($user.bonus)}{$user.bonus}{else}0{/if}
			</div>
		{/if}
		<div class="w1 m-border">
			<a href="mailto:{$user.email}" title="{$user.email}" class="small-descr">Написать</a>
		</div>
	{elseif $current_person_type == 'org'}
		<div class="w2 m-border">
			<strong>{$user.surname} {$user.name}</strong>
		</div>
		<div class="w{if $bonus_enable}2{else}3{/if} m-border">
			{$user.company_name}
		</div>
		{if $bonus_enable}
			<div class="w1 m-border">
				{if !empty($user.bonus)}{$user.bonus}{else}0{/if}
			</div>
		{/if}
		<div class="w1 m-border">
			<a href="mailto:{$user.email}" title="{$user.email}" class="small-descr">Написать</a>
		</div>
	{/if}
	<div class="w1 m-border" title="{if $user.status == "active"}Активный{else}Забаненный{/if}">
		<div class="user-status m-{$user.status}"></div>
	</div>
	{if $account->isPermission('users-edit', 'editUser')}
		{if $accountType != 'SuperAdmin' && $user.role == 'SuperAdmin'}
			<div class="w1 m-border"></div>
		{else}
			<div class="w1 m-border action-button action-edit" title="Редактировать">
				<i class="icon-edit"></i>
			</div>
		{/if}
	{else}<div class="w1 m-border"></div>{/if}
	{if $account->isPermission('users-edit', 'editUser')}
		{if $accountType != 'SuperAdmin' && $user.role == 'SuperAdmin'}
			<div class="w1 m-border"></div>
		{else}
			<div class="w1 m-border action-button action-delete" title="Удалить">
				<i class="icon-delete"></i>
			</div>
		{/if}
	{else}<div class="w1 m-border"></div>{/if}
</div>
