{include file="Modules/Profile/EditUsers/userHeader.tpl"}
<div class="white-body">
	{if !empty($users)}
		{foreach from=$users item=user name=user_list}
			{include file="Modules/Profile/EditUsers/userRow.tpl" user=$user}
		{/foreach}
	{else}
		<div class="wblock white-block-row">
			<div class="w12">Пользователи не найдены</div>
		</div>
	{/if}
</div>