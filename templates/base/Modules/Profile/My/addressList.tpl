{?$current_user = $account->getUser()}
{?$addresses = $current_user->getAddresses()}
{if !empty($addresses)}
	<ul class="pr-addresses">
		{foreach from=$addresses key=adr_id item=adr_text}
			<li>
				<i class="icon i-adds"></i>
				<p>{$adr_text}</p>
				<a href="#" class="delete edit-link" data-id="{$adr_id}">Удалить</a>
			</li>
		{/foreach}
	</ul>
{else}
	<div class="empty-result">У вас ещё нет адресов.</div>
{/if}	