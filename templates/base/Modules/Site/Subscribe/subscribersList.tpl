{if !empty($members)}
<div class="wblock white-block-row white-header" {if !empty($filter_params)}data-filter-params='{$filter_params}'{/if}>
	<label class="w05"><input type="checkbox" class="check-all" /></label>
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.surname)}
		{?$current_sort = 1}
		{if $quicky.get.order.surname == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<div class="w3">
		<a class="sort-link{if $current_sort} m-sort-{$sort_val}{/if}" href="?group_id={$group.id}&order[surname]={$sort_val}" data-sort="order[surname]" data-val="{$sort_val}">Фамилия</a>&nbsp;и&nbsp;

		{?$current_sort = 0}
		{if !empty($quicky.get.order) && isset($quicky.get.order.company_name)}
			{?$current_sort = 1}
			{if $quicky.get.order.company_name == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
		{else}{?$sort_val = 0}{/if}

		<a class="sort-link{if $current_sort} m-sort-{$sort_val}{/if}" href="?group_id={$group.id}&order[company_name]={$sort_val}" data-sort="order[company_name]" data-val="{$sort_val}">организация</a>
	</div>
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.email)}
		{?$current_sort = 1}
		{if $quicky.get.order.email == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a class="w2 sort-link{if $current_sort} m-sort-{$sort_val}{/if}" href="?group_id={$group.id}&order[email]={$sort_val}" data-sort="order[email]" data-val="{$sort_val}">
		Email
	</a>
	<div class="w1">
		Списки
	</div>
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.create_time)}
		{?$current_sort = 1}
		{if $quicky.get.order.create_time == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a class="w2 sort-link{if $current_sort} m-sort-{$sort_val}{/if}" href="?group_id={$group.id}&order[create_time]={$sort_val}" data-sort="order[create_time]" data-val="{$sort_val}">
		В базе с
	</a>
	<div class="w05"></div>
	{?$current_sort = 0}
	{if !empty($quicky.get.order) && isset($quicky.get.order.lockremove)}
		{?$current_sort = 1}
		{if $quicky.get.order.lockremove == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a class="w1 sort-link{if $current_sort} m-sort-{$sort_val}{/if}" href="?group_id={$group.id}&order[lockremove]={$sort_val}" data-sort="order[lockremove]" data-val="{$sort_val}">
		Статус
	</a>
	<div class="w2"></div>
</div>
<div class="white-body">
	{foreach from=$members item=member name=subcriberslist}
		{?$member_inner = !empty($inner_members[$member.email])}
		<div class="wblock white-block-row{if iteration%2 != 0} odd{/if}{if $member_inner} s-client{/if}" data-email="{$member.email}" data-name="{$member.name}">
			<div class="w05">
				<input class="selected-subscriber" type="checkbox" name="subscribers[]" value="{$member.email}">
			</div>
			<div class="w3">
				{?$name_parts = explode(' ', $member.name)}
				{?$initials = ''}
				{foreach from=$name_parts item=name_part}
					{?$initials .= mb_substr($name_part, 0, 1, 'UTF-8') . '.'}
				{/foreach}
				<span style="font-weight:800;">{$member.surname}&nbsp;{$initials}</span><br>
				{$member.company_name}
			</div>
			<div class="w2">
				<a href="mailto:{$member.email}" title="{$member.email}">{if strlen($member.email) > 24}{substr($member.email, 0, 15)}...{else}{$member.email}{/if}</a>
			</div>
			<div class="w1 dropdown">
				<span class="dropdown-toggle">{$member.groups|count}</span>
				<div class="subscr-list dropdown-menu a-hidden">
					<ul>
						{foreach from=$member.groups item=group_id}
							{if !empty($groups[$group_id]) && $groups[$group_id].type == 'list'}
								<li><a href="/subscribe/subscribers/?group_id={$groups[$group_id].id}">{$groups[$group_id].name}</a></li>
							{/if}
						{/foreach}
					</ul>
				</div>
			</div>
			<div class="w2">{date('d.m.Y', strtotime($member['create_time']))}</div>
			<div class="w05"></div>
			<div class="w1 user-status m-{if !empty($member.lockremove)}banned{elseif !empty($member.lockconfirm)}new{else}active{/if}" title="{if !empty($member.lockremove)}Разблокировать{elseif !empty($member.lockconfirm)}Не подтвержден{else}Заблокировать{/if}">
				<div></div>
			</div>
			<a class="w1 action-button action-edit m-border {if !$member_inner}m-active{/if}" title="Редактировать"{if $member_inner} href="/users-edit/?type=client&email={$member.email}"{else} href="#" data-email="{$member.email}"{/if}><i class="icon-edit"></i></a>
			{if !in_array($group.id, array('main', 'main_fiz'))}
				<a href="/subscribe/deleteSubscribers/?email[]={$member.email}&group={$group.id}" class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></a>
			{else}
				<div class="w1"></div>
			{/if}
		</div>
	{/foreach}
</div>
{else}
	<div class="white-body">
		<div class="wblock white-block-row">
			<div class="w12">Нет подписчиков</div>
		</div>
	</div>
{/if}
