{if !empty($phrases)}
<div class="wblock white-header white-block-row">
	{?$current_sort = 0}
	{if !empty($order) && isset($order.phrase)}
		{?$current_sort = 1}
		{if $order.phrase == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a href="?sort[phrase]={$sort_val}" class="w4 sort-link{if $current_sort} m-sort-{abs($sort_val-1)}{/if}" data-sort="sort[phrase]" data-val="{$sort_val}">Фраза</a>
	{?$current_sort = 0}
	{if !empty($order) && isset($order.url)}
		{?$current_sort = 1}
		{if $order.url == 1}{?$sort_val = 0}{else}{?$sort_val = 1}{/if}
	{else}{?$sort_val = 0}{/if}
	<a href="?sort[url]={$sort_val}" class="w6 sort-link{if $current_sort} m-sort-{abs($sort_val-1)}{/if}" data-sort="sort[url]" data-val="{$sort_val}">URL</a>
	<div class="w2"></div>
</div>
<div class="white-body">
{foreach from=$phrases key=id item=phrase}
	<div class="wblock white-block-row" data-id="{$phrase.id}">
		<div class="w4">
			{$phrase.phrase}
		</div>
		<a href="{$phrase.url}" class="w6" target="_blank">{$phrase.url}</a>
		<div class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></div>
		<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
	</div>
  {*  <div class="wblock white-block-row">
        <div class="w4 td-title"><span class="data_phrase editPhrase a-link">{$phrase_data.phrase}</span></div>
        <div class="w7"><a href="{$phrase_data.url}" target="_blank"><span class="data_url">{$phrase_data.url}</span></a></div>
        <a href="#" class="action-button action-delete w1" title="Смотреть на сайте"><i></i></a>
    </div>*}
{/foreach}
</div>
{else}
<div class="white-body">
	<div class="wblock white-block-row">
		<div class="w12">
			Фразы не созданы
		</div>
	</div>
</div>	
{/if}