{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Купить элитную квартиру в СПб на «вторичке» | Агентство «М16-Недвижимость»'}
	{?$pageDescription = "Представляем вам каталог квартир на «вторичке» в центре СПб и других районах" .
		"города. Цены, планировки, фото, квартиры с 1-6 спальнями, готовая отделка и без отделки" .
		"– в активе компании огромное множество достойных предложений."}
{else}
	{?$pageTitle = 'Buy an elite apartment in St. Petersburg on the "secondary housing" | Agency "M16-Real Estate"'}
	{?$pageDescription = "We present you the catalog of apartments on the \"secondary housing\" in the center of St. ".
	"Petersburg and other areas cities. Prices, lay-outs, photos, apartments with 1-6 bedrooms, finished finishings and without finishing".
		"- the company has a huge number of worthy offers."}
{/if}
{if !empty($canonical)}
    {? $canonical_uri = $canonical}
{/if}
{if isset($seoItem)}
	{if $for_catalog === true && !empty($catalogKey)}
		{if !empty($seoItem['page_title_' . $catalogKey])}{?$pageTitle = $seoItem['page_title_' . $catalogKey]}{/if}
		{if !empty($seoItem['page_description_' . $catalogKey])}{?$pageDescription = $seoItem['page_description_' . $catalogKey]}{/if}
	{else}
		{if !empty($seoItem['page_title'])}{?$pageTitle = $seoItem['page_title']}{/if}
		{if !empty($seoItem['page_description'])}{?$pageDescription = $seoItem['page_description']}{/if}
	{/if}

	{if isset($seoItem['is_template']) && $seoItem['is_template'] === 1}

		{if isset($filter_beds_number)}
            {?$pageTitle = $pageTitle|replace:'BEDROOMS_NUMERIC':$filter_beds_number}
            {?$pageDescription = $pageDescription|replace:'BEDROOMS_NUMERIC':$filter_beds_number}
		{/if}

        {if isset($filter_beds_number_synonym)}
            {?$pageTitle = $pageTitle|replace:'BEDROOMS_WORD':$filter_beds_number_synonym}
            {?$pageDescription = $pageDescription|replace:'BEDROOMS_WORD':$filter_beds_number_synonym}
        {/if}

        {if isset($seoDistrict)}
            {?$pageTitle = $pageTitle|replace:'DISTRICT':$seoDistrict['prepositional']}
            {?$pageDescription = $pageDescription|replace:'DISTRICT':$seoDistrict['prepositional']}
        {/if}

	{/if}

{/if}



<div class="top-bg">
	<div class="site-top-arenda site-top">
		<h1 class="title" title="{$lang->get('Аренда элитной недвижимости', 'Luxury resale residential property')}">
			{$lang->get('<span>Аренда</span><br>элитной недвижимости', '<span>Luxury resale</span><br>residential property')|html}
		</h1>
		<div class="main">{$lang->get('В Санкт-Петербурге', 'In St.Petersburg')}</div>
	</div>
</div>
<div class="swiper">
	<div class="swiper-container">
		<div class="swiper-wrapper">
				<a href='/arenda/#'><div class="swiper-slide">
				<img src="/data/thumbs/w2500h529/70cba3/single/1033.jpg?1471267280" alt=""></div>
				</a>
		</div>
	</div>
</div>
<form method="GET" class="filter">
	{include file='Modules/Catalog/Arenda/filter_fields.tpl'}
	<div class="field order">
		<div class="page-center">
			<div class="search">
				<div class="lens">{fetch file=$path . "search.svg"}</div>
				<input type="text" name="title" placeholder='{$lang->get('Поиск по адресу', 'Search by address')}' />
			</div>
			<div>{$lang->get('Сортировка', 'Sort')}</div>
			<label>{$lang->get('Без сортировки', 'no sorting')}<input type="checkbox" name="order[]" data-radio='.order' value="" data-default class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По адресу', 'by address')}<input type="checkbox" name="order[title]" data-radio='.order' value="1" class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По цене', 'by price')}<input type="checkbox" name="order[close_price]" data-radio='.order' value="1" class="m-hidden-input order-radio"></label>
			<div>{$lang->get('Отображение', 'View as')}</div>
			<label>{$lang->get('Списком', 'List')}<input type="radio" name="view" value="list" data-radio='.view' data-default class="m-hidden-input view-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('На карте', 'Map')}<input type="radio" name="view" value="map" data-radio='.view' class="m-hidden-input view-radio"></label>
		</div>
	</div>
</form>

<div class="filter-result">
	{include file='Modules/Catalog/Arenda/apartmentsList.tpl'}
</div>
<div class="quickview-cont prevent-scroll"></div>
<div class="resale">
	<div class="wrap">
	<div class="descr">{$lang->get('А почему бы не изучить предложения в строящихся домах?', 'Why not check some offers in buildings under construction?')}</div>
	<a href="{$url_prefix}/real-estate/" class="btn m-light-magenta">{$lang->get('смотреть квартиры в новых домах', 'Search in new objects')}</a>
	</div>
</div>
<div class="resale">
	<div class="wrap">
		<div class="descr">{$lang->get('Мы готовы взять труд по подбору идеальной квартиры на себя', 'We are willing to take the trouble of choosing the perfect apartament ')}</div>
		<div class="buttons">
			<a href="{$url_prefix}/selection/" class="btn m-light-magenta">{$lang->get('Оставить заявку', 'Send your request')}</a>
		</div>
	</div>
</div>
{include file='/components/about.tpl' wife=1 items_list_flag=1 main_about_text=$lang->get('Аренда элитной недвижимости', 'Luxury apartments for resale')}



{include file='/components/itemsListMicroMark.tpl'}

