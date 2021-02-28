{if !isset($bonus_enable)}
	{?$order_config = $site_config->get(null, 'order')}
	{if !empty($order_config)}
		{?$bonus_enable = $order_config.properties.bonus_enable.value}
	{else}
		{?$bonus_enable = 0}
	{/if}
{/if}
<div class="wblock white-header white-block-row">
	<div class="w1">
		ID
	</div>
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.reg_date)}
		{?$current_sort = 1}
		{if $quicky.get.order.reg_date == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a href="?type={$current_person_type}&order[reg_date]={$sort_val}" data-sort="order[reg_date]" data-val="{$sort_val}"class="sort-link w2{if $current_sort} m-sort-{$sort_val}{/if}">
		Создан
	</a>
	{if $current_person_type == 'man'}
		{?$current_sort = 0}
		{if !empty($quicky.get.order) && isset($quicky.get.order.email)}
			{?$current_sort = 1}
			{if $quicky.get.order.email == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
		{else}{?$sort_val = 0}{/if}
		<a href="?type={$current_person_type}&order[email]={$sort_val}" data-sort="order[email]" data-val="{$sort_val}" class="sort-link w3{if $current_sort} m-sort-{$sort_val}{/if}">
			Логин
		</a>
		<div class="w3">
			Роль
		</div>
	{elseif $current_person_type == 'fiz'}
		{?$current_sort = 0}
		{if !empty($quicky.get.order) && isset($quicky.get.order.full_name)}
			{?$current_sort = 1}
			{if $quicky.get.order.full_name == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
		{else}{?$sort_val = 0}{/if}
		<a href="?type={$current_person_type}&order[full_name]={$sort_val}" data-sort="order[full_name]" data-val="{$sort_val}" class="sort-link w{if $bonus_enable}4{else}5{/if}{if $current_sort} m-sort-{$sort_val}{/if}">
			Фамилия, имя
		</a>
		{if $bonus_enable}
			<div class="w1">
				Бонус
			</div>
		{/if}
		<div class="w1">
			E-mail
		</div>	
	{elseif $current_person_type == 'org'}
		{?$current_sort = 0}
		{if !empty($quicky.get.order) && isset($quicky.get.order.full_name)}
			{?$current_sort = 1}
			{if $quicky.get.order.full_name == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
		{else}{?$sort_val = 0}{/if}
		<a href="?type={$current_person_type}&order[full_name]={$sort_val}" data-sort="order[full_name]" data-val="{$sort_val}" class="sort-link w2{if $current_sort} m-sort-{$sort_val}{/if}">
			Фамилия, имя
		</a>
		{?$current_sort = 0}
		{if !empty($quicky.get.order) && isset($quicky.get.order.company_name)}
			{?$current_sort = 1}
			{if $quicky.get.order.company_name == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
		{else}{?$sort_val = 0}{/if}
		<a href="?type={$current_person_type}&order[company_name]={$sort_val}" data-sort="order[company_name]" data-val="{$sort_val}" class="sort-link w{if $bonus_enable}2{else}3{/if}{if $current_sort} m-sort-{$sort_val}{/if}">
			Организация
		</a>
		{if $bonus_enable}
			<div class="w1">
				Бонус
			</div>
		{/if}	
		<div class="w1">
			E-mail
		</div>
	{/if}
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.status)}
		{?$current_sort = 1}
		{if $quicky.get.order.status == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a href="?type={$current_person_type}&order[status]={$sort_val}" data-sort="order[status]" data-val="{$sort_val}" class="sort-link w1{if $current_sort} m-sort-{$sort_val}{/if}">
		Статус
	</a>
	<div class="w1"></div>
	<div class="w1"></div>
</div>