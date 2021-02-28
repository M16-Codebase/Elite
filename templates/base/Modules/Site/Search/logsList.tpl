<div class="wblock white-header white-block-row">
	{?$current_sort = 0}
	{if !empty($order) && isset($order.phrase)}
		{?$current_sort = 1}
		{if $order.phrase == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<div class="w4"><a href="?sort[phrase]={$sort_val}" class="sort-link{if $current_sort} m-sort-{abs($sort_val-1)}{/if}" data-sort="sort[phrase]" data-val="{$sort_val}">Фраза</a></div>
	{?$current_sort = 0}
	{if !empty($order) && isset($order.count)}
		{?$current_sort = 1}
		{if $order.count == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<div class="w4"><a href="?sort[count]={$sort_val}" class="sort-link{if $current_sort} m-sort-{abs($sort_val-1)}{/if}" data-sort="sort[count]" data-val="{$sort_val}">Количество</a></div>
	{?$current_sort = 0}
	{if !empty($order) && isset($order.date)}
		{?$current_sort = 1}
		{if $order.date == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<div class="w4"><a href="?sort[date]={$sort_val}" class="sort-link{if $current_sort} m-sort-{abs($sort_val-1)}{/if}" data-sort="sort[date]" data-val="{$sort_val}">Последний поиск</a></div>
</div>
<div class="white-body">
{if !empty($logs)}
	{foreach from=$logs item=$l}
		<div class="wblock white-block-row">
			<div class="w4">{$l.phrase}</div>
			<div class="w4">{$l.count}</div>
			<div class="w4">{strtotime($l.date)|date_format:'%d.%m.%Y %H:%M'}</div>
		</div>
	{/foreach}
{else}
	<div class="wblock white-block-row">
		<div class="w12">Поисковые запросы отсутствуют</div>
	</div>	
{/if}
</div>