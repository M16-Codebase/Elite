{strip}
{*
	параметры:
		count - общее число записей
		pageSize - размер страницы
		pageNum - текущая страница
		url - адрес, должен содержать %d для номера страницы (по дефолту $smarty.server.REQUEST_URI)
		param - параметр страницы (по дефолту "page")
		show - количество показываемых страниц в навигаторе, по умолчанию 9
		scroll_postfix - параметр id элемента к которому прокрутить страницу
*}

{assign var="tmp_parametr" value=$param|default:"page"}
{assign var="tmp_url" value=$url|default:$smarty.server.REQUEST_URI}
{if !isset($pageNum)}
	{if isset($smarty.get[$tmp_parametr])}
		{assign var="pageNum" value=$smarty.get[$tmp_parametr]}
	{else}
		{assign var="pageNum" value="0"}
	{/if}
{/if}
{if (strpos($tmp_url,'%d')===false)}
	{assign var="tmp_parametr_string" value="/\?$tmp_parametr=[0-9]+&?/"}
	{assign var="tmp_url" value=$tmp_url|regex_replace:"/&?$tmp_parametr=[0-9]*/":""}
	{assign var="tmp_url" value=$tmp_url|regex_replace:"/\?$/":""}

	{if strpos($tmp_url, '?')}
		{assign var="tmp_url" value="`$tmp_url`&`$tmp_parametr`=%d"}
	{else}
		{assign var="tmp_url" value="`$tmp_url`?`$tmp_parametr`=%d"}
	{/if}
{/if}

{assign var="tmp_show" value=$show|default:9}
{assign var="tmp_pageSize" value=$pageSize|default:10}

{math equation="ceil(count/page_size)" count=$count page_size=$tmp_pageSize assign="tmp_total"}
{math equation="min(page, total)" page=$pageNum total=$tmp_total assign="pageNum"}

{math equation="max(1, min(ceil(page-show/2), total-show+1))" page=$pageNum total=$tmp_total show=$tmp_show assign="tmp_start"}
{math equation="min(start+show-1, total)" start=$tmp_start show=$tmp_show total=$tmp_total assign="tmp_finish"} 

{math equation="page-1" page=$pageNum assign="tmp_prev"}
{math equation="page+1" page=$pageNum assign="tmp_next"}




{if $tmp_total > 1}
	{if !empty($scroll_postfix)}
		{assign var="tmp_scroll_postfix" value="#$scroll_postfix"}
	{else}
		{assign var="tmp_scroll_postfix" value=''}
	{/if}
	<div class="paging white-blocks">
		<div class="wblock white-block-row">
			{if $tmp_prev > 0}
				<a href="{$tmp_url|replace:"%d":""}{$tmp_prev}{$tmp_scroll_postfix}" data-page="{$tmp_prev}" class="prev w1 action-button action-back" title="Предыдущая страница">
					<i class="icon-back"></i>
				</a>
			{else}
				<div class="w1"></div>
			{/if}
			<div class="pages w10 a-inline-cont{if $tmp_prev > 0} m-border{/if}">
				{*первую выводим всегда*}
				<a href="{$tmp_url|replace:"%d":"1"}"{if $pageNum == 1} class="m-current"{/if}>1</a>
				{if $tmp_start > 2}
					<span class="dots">...</span>
				{/if}
				{section loop=$tmp_total+1 start=$tmp_start max=$tmp_show name="paging"}
					{if !in_array($smarty.section.paging.index, array(1, $tmp_total))}
						<a href="{$tmp_url|replace:"%d":""}{$smarty.section.paging.index}{$tmp_scroll_postfix}"{if $smarty.section.paging.index == $pageNum} class="m-current"{/if}>{$smarty.section.paging.index}</a>
					{/if}
				{/section}
				{if $tmp_start + $tmp_show < $tmp_total}
					<span class="dots">...</span>
				{/if}
				{*последнюю выводим всегда*}
				<a href="{$tmp_url|replace:"%d":""}{$tmp_total}"{if $pageNum == $tmp_total} class="m-current"{/if}>{$tmp_total}</a>
			</div>
			{if $tmp_next <= $tmp_total}
				<a href="{$tmp_url|replace:"%d":""}{$tmp_next}{$tmp_scroll_postfix}" data-page="{$tmp_next}" class="next w1 action-button action-next m-border" title="Следующая страница">
					<i class="icon-next"></i>
				</a>
			{else}
				<div class="w1"></div>
			{/if}
		</div>
	</div>
{/if}
{/strip} 
