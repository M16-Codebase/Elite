{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Гид по районам Санкт-Петербурга | М16-Недвижимость'}
	{?$pageDescription = 'Информация о самых востребованных районах элитной недвижимости в Санкт-Петербурге'}
{else}
	{?$pageTitle = 'St.Petersburg districts guidebook | M16 Real Estate Agency'}
	{?$pageDescription = 'Real estate secured loan, individual search, the investment tour, urgent buyout — these services and other are available to every client of M16 Real Estate Agency'}
{/if}
{*<h1>{$saint_petersburg.title}</h1>*}
{?$delim = ldelim . "!" . rdelim}

{?$gravity = array(
		'TL' => 'top left',
		'T' => 'top center',
		'TR' => 'top right',
		'L' => 'left center',
		'C' => 'center center',
		'R' => 'right center',
		'BL' => 'bottom left',
		'B' => 'bottom center',
		'BR' => 'bottom right',
	)}
<div class="top-bg" id="site-top">
	<a href="{$url_prefix}/" class="back">{fetch file=$path . "arrow.svg"}</a>
	<div class='bg-img' style='background: url(/img/veil.png), url(/img/saint-p.jpg);background-size:cover;'></div>
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Гид по районам Санкт-Петербурга', 'City area guide of St.Petersburg')}">{$lang->get('<span>Гид по районам</span><br>Санкт-Петербурга', '<span>City area guide</span><br>of St.Petersburg')|html}</h1>
		<a href="{$url_prefix}/contacts/#form" class="btn m-sand">{$lang->get('Получить консультацию лично', 'Get your personal advice')}</a>
	</div>
</div>
<div class="white-wrap">
	<div class="districts-wrap">
		{include file='Modules/Catalog/District/districtList.tpl' items=$spb_districts}
	</div>
</div>