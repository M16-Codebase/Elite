<span>Сортировать по</span>
{?$sort_url = $quicky.server['QUERY_STRING']|regex_replace:'/\&?sort\[\w+\]\=\d/i':''}
{?$sort_url = $sort_url|regex_replace:'/\&?page\=\d+/i':''}
{if !empty($sort_url)}
	{?$sort_url .= '&'}
{/if}
{?$sort_url = $quicky.server['REDIRECT_URL'] . '?' . $sort_url}
<div class="dropdown hoverable a-inline-block">
	<div class="header-menu-link dd-arrow a-link">
		{if (isset($sort.price) && $sort.price == 0) || (isset($quicky.get.sort) && isset($quicky.get.sort.price) && $quicky.get.sort.price == 0)}		
			Цене, сначала недорогие
		{else}
			Цене, сначала дорогие
		{/if}
	</div>
	<ul class="dropdown-menu a-hidden">
		<li><a href="{$sort_url}sort[price]=0" class="sort-link" data-sort="sort[price]" data-val="0"><i class="a-inline-block"></i>Цене, сначала недорогие</a></li>
		<li><a href="{$sort_url}sort[price]=1" class="sort-link" data-sort="sort[price]" data-val="1"><i class="a-inline-block"></i>Цене, сначала дорогие</a></li>
	</ul>
</div>