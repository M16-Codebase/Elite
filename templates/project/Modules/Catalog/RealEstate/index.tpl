
{*
{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Строящаяся элитная недвижимость | М16-Недвижимость'}
	{?$pageDescription = 'Подбор лучших элитных квартир в новостройках Санкт-Петербурга от агентства недвижимости Вячеслава Малафеева М16-Недвижимость'}
{else}
	{?$pageTitle = 'New Buildings | M16 Real Estate Agency'}
	{?$pageDescription = 'Selection of the best luxury apartments in new buildings of St.Petersburg at M16 Real Estate Agency of Vyacheslav Malafeyev'}
{/if}
*}


{if $request_segment.key == 'ru'}
    {?$pageTitle = "Элитные новостройки Санкт-Петербурга | Купить квартиру в «М16-Недвижимость»"}
    {?$pageDescription = "Элитные жилые комплексы Санкт-Петербурга и лучшие новостройки бизнес-класса – в агентстве «М16-Недвижимость». Разные районы города, дома у воды и рядом с парками, уникальные характеристики и продуманные планировки."}
{else}
    {?$pageTitle = "Elite new buildings in St. Petersburg | Buy an apartment in \"M16-Real Estate\""}
    {?$pageDescription = "Elite residential complexes of St. Petersburg and the best new business-class developments - in the agency \"M16-Real Estate\". Different parts of the city, houses by the water and next to Parks, unique characteristics and thoughtful planning."}
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

<div class="top-bg m-white">
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Строящаяся элитная недвижимость', 'New premium class residential property')}">
			{$lang->get('<span>Строящаяся</span><br>элитная недвижимость', '<span>New premium class</span><br>residential property')|html}
		</h1>
		<div class="main">{$lang->get('В Санкт-Петербурге', 'In St.Petersburg')}</div>
	</div>
</div>
{if !empty($page_banners)}
	<div class="swiper">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				{foreach from=$page_banners item=slide name=slide_n}
					{if !empty($slide.image)}
						{if !empty($slide.destination)}<a {if $slide.link_type == 'external'}rel='nofollow' target='_blank'{/if} href='{if $slide.link_type == 'external'}http://{/if}{$slide.destination}'{else}<div{/if} class="swiper-slide">
							<img src="{$slide.image->getUrl(2500,529)}" alt="">
						</{if !empty($slide.destination)}a{else}div{/if}>
					{/if}
				{/foreach}
			</div>
			<div class="swiper-pagination{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}"></div>
			<div class="swiper-button-prev{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
			<div class="swiper-button-next{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		</div>
	</div>
{/if}
<form method="GET" class="filter">
	{include file='Modules/Catalog/RealEstate/filter_fields.tpl'}
	<div class="field order">
		<div class="page-center">
			<div class="search">
				<div class="lens">{fetch file=$path . "search.svg"}</div>
				<input type="text" name="title" placeholder='{$lang->get('Поиск по названию', 'Search by title')}' />
			</div>
			<div>{$lang->get('Сортировка', 'Sort')}</div>
			<label>{$lang->get('Без сортировки', 'No sorting')}<input type="checkbox" name="order[]" data-radio='.order' value="" data-default class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По названию', 'By title')}<input type="checkbox" name="order[title]" data-radio='.order' value="1" class="m-hidden-input order-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('По цене', 'By price')}<input type="checkbox" name="order[close_price]" data-radio='.order' value="1" class="m-hidden-input order-radio"></label>
			<div>{$lang->get('Отображение', 'View as')}</div>
			<label>{$lang->get('Списком', 'List ')}<input type="radio" name="view" value="list" data-radio='.view' data-default class="m-hidden-input view-radio"></label>
			<span class="slash"></span>
			<label>{$lang->get('На карте', 'Map')}<input type="radio" name="view" value="map" data-radio='.view' class="m-hidden-input view-radio"></label>
		</div>
	</div>
</form>

<div class="filter-result">
	{include file='Modules/Catalog/RealEstate/complexList.tpl'}
</div>
<div class="resale m-center">
	<div class="descr">{$lang->get('А почему бы не изучить похожие предложения в готовых домах?', 'Why not check some apartments fo resale?')}</div>
	<a href="{$url_prefix}/resale/" class="btn m-light-magenta">{$lang->get('Искать на вторичном рынке', 'Search for resale apartments')}</a>
</div>
{include file='/components/about.tpl' wife=1 items_list_flag=1}

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
	{include file='/components/real_estate_seo_text.tpl'}
{/if}