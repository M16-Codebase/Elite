<header class="page-header">
	{if $accountType != 'Guest'}
		{$account->getUser()->getEmail()}<br />
		{if $accountType == 'Admin' || $accountType == 'SuperAdmin'}
			<a href="/catalog-admin/">Админка</a><br />
		{/if}
		<a href="/logout/">Выход</a>
	{else}
		{*{include file="components/login.tpl"}*}
	{/if}
</header>