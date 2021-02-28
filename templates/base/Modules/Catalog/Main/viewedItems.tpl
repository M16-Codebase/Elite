<div class="content-block viewed-page">
	<div class="page-title">
		{include file="components/breadcrumb.tpl"}
		<h1>{if $request_segment.id==1}Просмотренные объекты{else}Selected offers{/if}</h1>
	</div>
	<div class="catalog-block viewed-block main-col">
		<div class="catalog-header viewed-header">
			<span class="group-offer"><i></i>{if $request_segment.id==1}Объекты{else}Objects{/if} <span>{count($viewed_items)}</span></span>
		</div>
	</div>
	<div class="viewed-result{if empty($viewed_items)} empty-viewed-result{/if}">
		{if !empty($viewed_items)}
			<ul>
				{?$i=0}
				{foreach from=$viewed_items item=item_data name=item_i}
					{?$i++}
					{?$catalog_item = $item_data.item}
					{?$days = $item_data.days}
					{?$item_type = $catalog_item->getType()}
						{if $item_type.id=='63' || $item_type.id=='64'}
							{?$offer_type = ($request_segment.id == 1) ? 'Офисы' : 'Office real estate'}
						{elseif $item_type.id=='65' || $item_type.id=='67'}
							{?$offer_type = ($request_segment.id == 1) ? 'Производство и склады' : 'Production-warehouse'}
						{elseif $item_type.id=='59'}
							{?$offer_type = ($request_segment.id == 1) ? 'Земельный участок' : 'Land'}
							{?$type_stead=true}
						{elseif $item_type.id=='66' || $item_type.id=='68'}
							{?$offer_type = ($request_segment.id == 1) ? 'Торговая недвижимость' : 'Retail real estate'}
						{elseif $item_type.id=='62'}
							{?$offer_type = ($request_segment.id == 1) ? 'Жилая недвижимость' : 'Residential'}
							{?$type_apartments=true}
						{elseif $item_type.id=='61'}
							{?$offer_type = ($request_segment.id == 1) ? 'Инвестиционные проекты' : 'Investment projects'}
							{?$type_investment=true}
						{/if}
						{if $item_type.id=='63' || $item_type.id=='65' || $item_type.id=='66'}
							{?$sale_rent=($request_segment.id == 1) ? 'Аренда' : 'Rent'}
						{elseif $item_type.id=='64' || $item_type.id=='67' || $item_type.id=='68' || $item_type.id=='62'}
							{?$sale_rent=($request_segment.id == 1) ? 'Продажа' : 'Sale'}
						{/if}

						<li class="catalog-item link-wrap{if $i==1} m-first{elseif $i==5} m-last {?$i=0}{/if}">
							<a href="{$item_type->getUrl()}" class="type-title">{$offer_type}</a>
							<div class="item-cover">
								{if !empty($catalog_item.bez_komissii_offer) && ($catalog_item.bez_komissii_offer=='Да' || $catalog_item.bez_komissii_offer=='Yes')}
									<div class="marker">
										{if $request_segment.id==1}Без комиссии{else}No commission{/if}
									</div>
								{/if}
								{if !empty($catalog_item.gallery)}
									{?$images = $catalog_item.gallery->getImages()}
									{?$cover = $catalog_item.gallery->getCover()}
									{if empty($cover)}
										{?$cover = $catalog_item.gallery->getDefault()}
									{/if}
									{if !empty($cover)}
										<img src="{$cover->getUrl(204,128,true)}" alt="{$catalog_item.title}" />								
									{/if}
								{/if}
							</div>
							<div class="item-bottom">
								<a href="{$catalog_item->getUrl()}" class="item-title link-target">{$catalog_item.title}</a>
								<div class="item-info">
									{if !empty($sale_rent)}<span class="main">{$sale_rent}</span>{/if}
									{if !empty($type_apartments)}
										{if !empty($catalog_item.kolichestvo_spalen)}
											&nbsp;•&nbsp;<strong>{if $request_segment.id==1}{$catalog_item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}{else}{$catalog_item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}{/if}</strong>
										{/if}
									{else}
										{if !empty($catalog_item['special_variant'])}
											{?$special_offer = $catalog_item['special_variant']}
											{if !empty($special_offer.ploschad_ot_offer)}
												&nbsp;•&nbsp;<strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}</strong>
											{/if}
										{/if}
									{/if}
									{if !empty($type_stead)}
										<div class="item-location">
											{?$first_prop=true}
											{if !empty($catalog_item.rasstojanie_do_kad)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.rasstojanie_do_kad} от КАД{/if}
											{if !empty($catalog_item.rasstojanie_do_zsd)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$catalog_item.rasstojanie_do_zsd} от ЗСД{/if}
											{if !empty($catalog_item.napravlenie)}
												{foreach from=$catalog_item.napravlenie item=direction key=val_id}
													{if !$first_prop}, {else}{?$first_prop=false}{/if}{$direction}
												{/foreach}
											{/if}
										</div>
									{elseif !empty($type_apartments)}
										<div class="item-location">
											{$catalog_item.city_residential}
										</div>
									{else}
										{if !empty($catalog_item.properties.stantsii_metro) && $catalog_item.properties.stantsii_metro.value && !empty($metro_stations)}
											{foreach from=$catalog_item.properties.stantsii_metro.value item=metro_id name=metro_s}
												{if !empty($metro_stations[$metro_id]) && first}
													{?$metro = $metro_stations[$metro_id]}
													<div class="metro">
														<i></i>{$metro.title[$request_segment.id]}
														{if !empty($catalog_item.metro_data[$metro_id].distance)} — {$catalog_item.metro_data[$metro_id].distance} {if $request_segment.id==1}мин{else}min{/if}{/if}
													</div>
												{/if}
											{/foreach}
										{/if}
									{/if}
									<div class="watch-link"><i></i>
										{if $days==0}
											{if $request_segment.id==1}Сегодня{else}Today{/if}
										{elseif $days==1}
											{if $request_segment.id==1}Вчера{else}Yesterday{/if}
										{else}
											{if $request_segment.id==1}{$days|plural_form:"день","дня","дней"} назад{else}{$days} days ago{/if}
										{/if}
									</div>
								</div>
							</div>
						</li>
				{/foreach}
			</ul>
		{else}
			<div class="empty-result">
				<div class="empty-result-cont">
					<div class="empty-title">
						{if $request_segment.id==1}Список пуст{else}List is empty{/if}
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
<div class="catalog-bottom">
	<div class="green-line"></div>
	{include file="components/benefits.tpl"}
	{include file="components/news-block.tpl"}
	{include file="components/cbre-belt.tpl"}
</div>

{*
<h1>Недавно просмотренные объекты</h1>
{if empty($viewed_items)}
    Список пуст
{else}
    <ul>
        {foreach from=$viewed_items item=item_data}
            {?$item = $item_data.item}
            {?$days = $item_data.days}
            <li>
                <a href="#">{$item.title} {if $days==0}сегодня{elseif $days==1}вчера{else}{$days|plural_form:"день","дня","дней"} назад{/if}</a>
            </li>
        {/foreach}
    </ul>
{/if}


<form method="GET" action="/feedback/request/">
    <ul>
    {foreach from=$favorites.items item=item_data}
        {?$item = $item_data.item}
        <li>
            <h2>{$item.title}</h2>
            {foreach from=$item_data.variants item=offer}
                <p>
                    <input type="checkbox" name="id[]" value="{$offer.id}">
                    {$offer.variant_title}
                </p>
            {/foreach}
        </li>
    {/foreach}
    </ul>
    <input type="submit">
</form>
*}