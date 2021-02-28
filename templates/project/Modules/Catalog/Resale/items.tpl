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
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Вторичная элитная недвижимость', 'Luxury resale residential property')}">
			{$lang->get('<span>Вторичная</span><br>элитная недвижимость', '<span>Luxury resale</span><br>residential property')|html}
		</h1>
		<div class="main">{$lang->get('В Санкт-Петербурге', 'In St.Petersburg')}</div>
	</div>
</div>
{if !empty($page_banners)}
<div class="swiper">
	<div class="swiper-container">
		<div class="swiper-wrapper">
			{foreach from=$page_banners item=slide name=slide_n}
				{if !empty($slide.destination)}<a {if $slide.link_type == 'external'}rel='nofollow' target='_blank'{/if} href='{if $slide.link_type == 'external'}http://{/if}{$slide.destination}'{else}<div{/if} class="swiper-slide">
					<img src="{$slide.image->getUrl(2500,529)}" alt="">
				</{if !empty($slide.destination)}a{else}div{/if}>
			{/foreach}
		</div>
		<div class="swiper-pagination{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}"></div>
		<div class="swiper-button-prev{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		<div class="swiper-button-next{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
	</div>
</div>
{/if}
<form method="GET" class="filter">
	{include file='Modules/Catalog/Resale/filter_fields.tpl'}
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
	{include file='Modules/Catalog/Resale/apartmentsList.tpl'}
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
{include file='/components/about.tpl' wife=1 items_list_flag=1 main_about_text=$lang->get('Вторичная элитная недвижимость', 'Luxury apartments for resale')}


{if isset($is_friendly_url) && $is_friendly_url === true}
{*	для сео-текстов , вытащенных для запросов ЧПУ для фильтра
	или сео-текст для района, не важно
	ключ данной сущности в сео-айтеме должен быть page_seo_text *}
    {?$seoText = ''}
    {if $for_catalog === true && !empty($catalogKey)}
        {if isset($seoDistrict['page_seo_text_' . $catalogKey])}
            {? $seoText = $seoDistrict['page_seo_text_' . $catalogKey] }
        {elseif isset($seoItem['page_seo_text_' . $catalogKey])}
            {? $seoText = $seoItem['page_seo_text_' . $catalogKey] }
        {/if}
    {else}
        {if isset($seoDistrict['page_seo_text'])}
            {? $seoText = $seoDistrict['page_seo_text'] }
        {elseif isset($seoItem['page_seo_text'])}
            {? $seoText = $seoItem['page_seo_text'] }
        {/if}
    {/if}
	{include file='/components/custom_seo_text.tpl' text=$seoText }

	{* посылаем id района, для того чтобы отметить фильтр*}
	{? $data_district_id = ''}
	{if isset($filter_district_id)}
		{? $data_district_id = 'data-district = ' . $filter_district_id}
	{/if}

	{? $data_beds_number = ''}
	{if isset($filter_beds_number) && $filter_beds_number > 0}
		{? $data_beds_number = 'data-bed_number = ' . $filter_beds_number }
	{/if}
	<div style="display: none;" id="filter_data" {$data_district_id} {$data_beds_number}></div>
{else}
	{include file='/components/resale_seo_text.tpl'}
{/if}

{include file='/components/itemsListMicroMark.tpl'}
