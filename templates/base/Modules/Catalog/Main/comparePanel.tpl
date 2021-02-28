{?$ru = $request_segment.key == 'ru'}
{?$url_prefix = !$ru ? ('/' . $request_segment.key) : ''}
<div class="float-footer">
	<div class="footer-center justify">
		<a href="#" class="column h3 to-top scroll-to" data-target="0"><i></i>{if $ru}Наверх страницы{else}Top of the page{/if}</a>
		<a href="{$url_prefix}/catalog/compare/" class="column h3 compare-link">
			<i></i>{if $ru}В сравнении{else}For comparison{/if} <span class="num">{if !empty($compare_vars)}{count($compare_vars)}{else}0{/if}</span> <span>—</span> <span class="del clear-compare" title="{if $ru}Очистить{else}Clean{/if}"></span>
		</a>
		<a href="{$url_prefix}/catalog/favorites/" class="column h3 favorites-link">
			<i></i>{if $ru}В избранном{else}In favorites{/if} <span class="num">{if !empty($favorites.counts.variants)}{$favorites.counts.variants}{else}0{/if}</span> <span>—</span> <span class="del clear-favorites" title="{if $ru}Очистить{else}Clean{/if}"></span>
		</a>	
		{if $account->isPermission('site')}
			{?$current_user = $account->getUser()}
			<span class="column h3 admin-link">
				<span class="admin-cont">
					{if empty($current_user.name) && empty($current_user.surname)}
						{$current_user.email}
					{else}
						{if !empty($current_user.name)}{$current_user.name}{/if}
						{if !empty($current_user.surname)}{$current_user.surname}{/if}
					{/if}
					<span class="user-role">
						{?$user_role = $account->getUser()->getRole()}
						{if !empty($roles[$user_role])}
							({$roles[$user_role]['title']})
						{/if}
					</span>
					<a href="{if !empty($admin_link)}{$admin_link}{else}/site/{/if}" class="link-intranet" title="Интранет"><i></i></a>
					<a href="/logout/" class="link-logout" title="Выход"><i></i></a>
				</span>
			</span>
		{else}
			<a href="{$url_prefix}/catalog/viewedItems/" class="column h3 watch-link"><i></i>{if $ru}Вы смотрели{else}Your recently viewed pages{/if} <span class="num">{if !empty($viewed_items)}{count($viewed_items)}{else}0{/if}</span></a>
			<a href="#" class="column h3">{if $ru}Нужна консультация?{else}Need some advice?{/if}</a>
		{/if}
	</div>
</div>