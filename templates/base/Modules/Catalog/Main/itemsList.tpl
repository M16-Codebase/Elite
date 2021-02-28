{if !empty($catalog_items)}		
	{if empty($no_sort)}
		{if !empty($error_page)}
			<div class="sorting-panel m-with-nav-elems paging">
				{include file="components/sorting.tpl"}
				{include file="components/paging.tpl" arrows_only=1 show=4}
			</div>
		{/if}
	{/if}
	<div class="items-box clearfix">
		<div class="goods-area clearfix">
			{foreach from=$catalog_items item=item name=cat_item} 
				{include file="components/catalog-item.tpl" catalog_item=$item}
				{if iteration%3 == 0}</div><div class="goods-area clearfix">{/if}	
			{/foreach}
		</div>
	</div>
	{if empty($no_sort)}
		{if empty($on_print_page)}
			{include file="components/paging.tpl"}
		{/if}
	{/if}
{else}
	<div class="empty-result">
		<h3>По этому запросу ничего не найдено</h3>
		<p>Попробуйте сделать следующее:</p>
		<ul class="m-with-mdash">
			<li>Проверьте орфографическую правильность запроса</li>
			<li>Поищите товар в <a href="/catalog/" class="m-bold">каталоге</a></li>
			<li>Свяжитесь с нашими консультантами по телефону <span class="a-bold">{$site_config.office_contacts.phone[0]}</span></li>
		</ul>
	</div>
{/if}
