{?$delim = ldelim . "!" . rdelim}
{?$title = $complex.title|replace:$delim:' '}
{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Выбор квартиры — ' . $title|strip_tags . ' | М16-Недвижимость'}
	{?$pageDescription = $title|strip_tags . ' — подбор элитной квартиры в режиме онлайн. Выбор квартиры на схеме жилого комплекса или по интересующим вас характеристикам'}
{else}
	{?$pageTitle = 'Apartments selection — ' . $title|strip_tags . ' | M16 Real Estate Agency'}
	{?$pageDescription = $title|strip_tags . ' — luxury apartments selection online. Choose an apartment on the residential complex layout or according to specifications you are interested in'}
{/if}
<div class="top-bg">
	{if !empty($complex.gallery)}{?$complex_cover = $complex.gallery->getCover()}{/if}
	<a href="{$complex->getUrl()}" class="back a-hidden">{fetch file=$path . "arrow.svg"}</a>
	<div class="bg-img" style='background: url(/img/veil.png), url({!empty($complex_cover) ? $complex_cover->getUrl() : ''}); background-size:cover;'></div>
	<div class="site-top">
		<h1 class="title" title="{$title}">
			<span class="scheme-title">
				{if $select_mode == 'housing'}
					{$lang->get('Выбор корпуса', 'Choose building')}
				{else}
					{$lang->get('Выбор этажа', 'Choose floor')}
				{/if}
			</span>
			{$title}
		</h1>
		<div class="head-tabs">
			<a href="{$complex->getUrl()}apartments/" class="tab-title"><span>{$lang->get('Подбор по параметрам', 'Parameter search')}</span></a>
			<span class="tab-title m-current"><span>{$lang->get('Выбор на плане дома', 'Choose on building scheme')}</span></span>
		</div>
	</div>
</div>

<div class="scheme-block">
	{if $select_mode == 'housing'}
		{include file='Modules/Catalog/RealEstate/housingSelect.tpl'}
	{elseif $select_mode == 'floor'}
		{include file='Modules/Catalog/RealEstate/floorSelect.tpl'}
	{else}
		{include file='Modules/Catalog/RealEstate/apartSelect.tpl'}
	{/if}
</div>

<div class="resale m-center">
	<div class="descr">{$lang->get('Мы готовы взять труд по подбору идеальной квартиры на себя', 'We are willing to take the trouble of choosing the perfect apartament')}</div>
	<a href="{$url_prefix}/selection/" class="btn m-light-magenta">{$lang->get('Оставить заявку', 'Send your request')}</a>
</div>