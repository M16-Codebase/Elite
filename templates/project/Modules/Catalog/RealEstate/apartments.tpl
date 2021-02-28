{?$favorite_ids = $infoBlocks->get('favoriteIds', 'real-estate')}
{?$delim = ldelim . "!" . rdelim}
{?$title_arr = $delim|explode:$complex.title}
{?$title = $complex.title|replace:$delim:' '}
{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Выбор квартиры — ' . $title|strip_tags . ' | М16-Недвижимость'}
	{?$pageDescription = $title|strip_tags . ' — подбор элитной квартиры в режиме онлайн. Выбор квартиры на схеме жилого комплекса или по интересующим вас характеристикам'}
{else}
	{?$pageTitle = 'Apartments selection — ' . $title|strip_tags . ' | M16 Real Estate Agency'}
	{?$pageDescription = $title|strip_tags . ' — luxury apartments selection online. Choose an apartment on the residential complex layout or according to specifications you are interested in'}
{/if}
{if !empty($complex.gallery)}{?$cover = $complex.gallery->getCover()}{/if}
{include file='/components/main_menu.tpl'}
<div class="top-bg" id="site-top">
	{if !empty($complex->getUrl())}<a href="{$complex->getUrl()}" class="back">{fetch file=$path . "arrow.svg"}</a>{/if}
	<div class='bg-img' style='background: url(/img/veil.png), url({!empty($cover) ? $cover->getUrl() : ''});background-size:cover;'></div>
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Квартиры в доме', 'Apartments in')} {$title}"><span>{$lang->get('Квартиры в доме', 'Apartments in')}</span><br>{$title}</h1>
		<div class="head-tabs">
			<span class="tab-title m-current"><span>{$lang->get('Подбор по параметрам', 'Parameter search')}</span></span>
			<a href="{$complex->getUrl()}scheme/" class="tab-title"><span>{$lang->get('Выбор на плане дома', 'Choose on building scheme')}</span></a>
		</div>
	</div>
</div>
<form method="GET" class="filter user-form">
	{include file='Modules/Catalog/RealEstate/filter_fields.tpl'}
	<div class="field order">
		<div class="page-center">
			<div>{$lang->get('Сортировка', 'Sort')}</div>
			<label>{$lang->get('По площади', 'by area')} <input type="checkbox" name="order[area_all]" value="1" data-radio=".order" class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По цене', 'by price')} <input type="checkbox" name="order[close_price]" value="1" data-radio=".order" class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По спальням', 'by bedrooms')} <input type="checkbox" name="order[bed_number]" value="1" data-radio=".order" class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По этажности', 'by floor')} <input type="checkbox" name="order[floor_number]" value="1" data-radio=".order" class="m-hidden-input order-radio"></label>
		</div>
	</div>
</form>

<div class="bg-wrap flats-list">
	<div class="filter-result page-center">
		{include file='Modules/Catalog/RealEstate/apartmentsList.tpl'}
	</div>
</div>
<div class="callback-wrap">
	<div class="descr">{$lang->get('Мы готовы взять труд по подбору идеальной квартиры на себя', 'We are willing to take the trouble of choosing the perfect apartament ')}</div>
	<div class="buttons">
		<a href="{$url_prefix}/selection/" class="btn m-light-magenta">{$lang->get('Оставить заявку', 'Send your request')}</a>
	</div>
</div>
