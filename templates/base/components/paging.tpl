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
{assign var="tmp_url" value=$url|default:$smarty.server.REQUEST_URI}
{if (strpos($tmp_url,'%d')===false)}
	{assign var="tmp_parametr" value=$param|default:"page"}
	
	{assign var="tmp_parametr_string" value="/\?$tmp_parametr=[0-9]+&?/"}
	{assign var="tmp_url" value=$tmp_url|regex_replace:"/\?$tmp_parametr=[0-9]+&?/":"?"}
	{assign var="tmp_url" value=$tmp_url|regex_replace:"/&$tmp_parametr=[0-9]+/":""}

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
	<div class="paging a-inline-block a-inline-cont">
		<div class="pg-text">Страницы</div>
		<div class="pg-cont">
			{if $tmp_prev > 0}
			<a href="{$tmp_url|replace:"%d":""}{$tmp_prev}{$tmp_scroll_postfix}" class="pg-arrow a-left"></a>
			{/if}
			{if $tmp_next <= $tmp_total}
			<a href="{$tmp_url|replace:"%d":""}{$tmp_next}{$tmp_scroll_postfix}" class="pg-arrow a-right"></a>
			{/if}
			<ul class="pages a-inline-block a-inline-cont">
				{if $pageNum == 1}
					<li class="m-current"><a href="#">1</a></li>
				{else}
					<li><a href="{$tmp_url|replace:"&page=%d":""}">1</a></li>
				{/if}
				{if $tmp_start > 2}
					<li><span>...</span></li>
				{/if}
				{section loop=$tmp_total+1 start=$tmp_start max=$tmp_show name="paging"}
					{if in_array($smarty.section.paging.index, array(1, $tmp_total))}
					{elseif $smarty.section.paging.index == $pageNum}
						<li class="m-current"><a href="#">{$smarty.section.paging.index}</a></li>
					{else}
						<li><a href="{$tmp_url|replace:"%d":""}{$smarty.section.paging.index}{$tmp_scroll_postfix}">{$smarty.section.paging.index}</a></li>
					{/if}
				{/section}
				{if $tmp_start + $tmp_show < $tmp_total}
					<li><span>...</span></li>
				{/if}
				{if $pageNum == $tmp_total}
					<li class="m-current"><a href="#">{$tmp_total}</a></li>
				{else}
					<li><a href="{$tmp_url|replace:"%d":""}{$tmp_total}">{$tmp_total}</a></li>
				{/if}
			</ul>
		</div>
	</div>
{/if}