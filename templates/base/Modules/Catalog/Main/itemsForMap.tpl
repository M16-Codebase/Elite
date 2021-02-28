{include file="Admin/components/types_view.tpl"}

{if !empty($catalog_items)}
    {foreach from=$catalog_items item=item}
		{if !empty($item.koordinaty_na_karte)}
			{capture assign=itemInfo}
				<div class="object-info">
					{if !empty($item.gallery)}
						{?$cover = $item.gallery->getCover()}
						{if empty($cover)}
							{?$cover = $item.gallery->getDefault()}
						{/if}
						{if !empty($cover)}
							<a href="{$item->getUrl()}" class="cover">
								<img src="{$cover->getUrl(58,58,true)}" alt="{if !empty($item.title)}{$item.title}{/if}" />
							</a>
						{/if}
					{/if}	
					<div class="object-title"><a href="{$item->getUrl()}">{if !empty($item.title)}{$item.title}{/if}</a></div>
					{if !empty($item.city) || !empty($item.district)}
						<div class="small-descr">
							{?$first_prop=true}
							{if !empty($item.city)}{?$first_prop=false}{$item.city}{/if}
							{if !empty($item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.district}{/if},
						</div>
					{/if}
					{if !empty($item.adres)}	
						<div class="addr">{$item.adres}</div>
					{/if}
				</div>           
				{if !empty($item.stantsii_metro)}
					<ul class="object-metro a-inline-cont">						
						{foreach from=$item.properties.stantsii_metro.value item=metro_id}
							{if !empty($metro_stations[$metro_id])}
								{?$metro = $metro_stations[$metro_id]}
								<li>
									<i class="i-metro line-{$metro.line_id}"></i>{$metro.title[$request_segment.id]}
									{if !empty($item.metro_data[$metro_id].distance)} <span class="num small-descr">— {$item.metro_data[$metro_id].distance} м</span>{/if}
								</li>
							{/if}
						{/foreach}
					</ul>
				{/if}
			{/capture}
			<div class="item" data-id="{$item.id}" data-coords="{$item.koordinaty_na_karte}" data-title="{$item.title}" data-link="{$item->getUrl()}" data-info="{$itemInfo}">
				{?$offers = !empty($find_variants[$item['id']]) ? $find_variants[$item['id']] : NULL}
				{if !empty($offers)}
					{foreach from=$offers item=offer}
						{capture assign=offerInfo}
							<div class="offer-title">
								<a href="{$offer->getUrl()}" class="more-arrow">{$offer.variant_title}</a>
							</div>
							<div class="offer-info">
								<span class="main">{if $type_rent}{if $request_segment.id==1}АРЕНДА{else}RENT{/if}{else}{if $request_segment.id==1}ПРОДАЖА{else}SALE{/if}{/if}</span>
								{if !empty($offer.ploschad_range)}
									&nbsp;•&nbsp; <strong>
										{if empty($offer.ploschad_ot_offer) || empty($offer.ploschad_do_offer) || $offer.ploschad_ot_offer != $offer.ploschad_do_offer}
											{$offer.ploschad_range}
										{elseif !empty($offer.ploschad_ot_offer)}
											{$offer.ploschad_ot_offer}
										{elseif !empty($offer.ploschad_do_offer)}
											{$offer.ploschad_do_offer}
										{/if}										
									</strong>
								{/if}
								{if !empty($offer.price_variant)}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i> {$offer.price_variant}</strong>
								{else}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i> {if $request_segment.id==1}Цена договорная{else}Negotiated price{/if}</strong>
								{/if}
								{if !empty($offer.bez_komissii_offer) && $offer.bez_komissii_offer != 'Нет'}
									&nbsp;•&nbsp; <strong class="pink">{if $request_segment.id==1}Без комиссии{else}No commission{/if}</strong>
								{/if}
							</div>
							<div class="offer-buttons">
								<a href="{$offer->getUrl()}" class="offer-btn a-btn-green">{if $request_segment.id==1}Узнать больше{else}Learn more{/if}</a>
								<a href="{if $request_segment.id!=1}/en{/if}/feedback/request/?id[]={$offer.id}" class="offer-btn a-btn-green-light"><i class="i-order"></i>{if $request_segment.id==1}Оставить заявку{else}Submit a request{/if}</a>
								<div class="right-btn a-right">
									<a href="{$url_prefix}/catalog/favorites/" data-id="{$offer.id}" class="btn-favorites to-favorites{if !empty($favorites_vars[$offer.id])} in-favorites{/if}" title="{if $request_segment.id==1}Добавить в избранное{else}Add to favorites{/if}"><i></i></a>
									<a href="{$url_prefix}/catalog/compare/" data-id="{$offer.id}" class="btn-compare to-compare{if !empty($compare_vars[$offer.id])} in-compare{/if}" data-id="{$offer.id}" title="{if $request_segment.id==1}Добавить к сравнению{else}Add to compare{/if}"><i></i></a>
								</div>
							</div>
						{/capture}
						<div class="offer" data-title="{$offer.variant_title}" data-link="{$offer->getUrl()}" data-info="{$offerInfo}"></div>
					{/foreach}
				{elseif $type_apartments || $type_investment}
					{?$special_offer = $item['special_variant']}
					{capture assign=offerInfo}
						{if !empty($item.gallery)}
							{?$cover = $item.gallery->getCover()}
							{if empty($cover)}
								{?$cover = $item.gallery->getDefault()}
							{/if}
							{if !empty($cover)}
								<a href="{$item->getUrl()}" class="apt-cover a-left">
									<img src="{$cover->getUrl(170,114,true)}" alt="{if !empty($item.title)}{$item.title}{/if}" />
								</a>
							{/if}
						{/if}
						<div class="offer-apt-cont">
							<div class="offer-title">
								<a href="{$item->getUrl()}" class="more-arrow">{$item.title}</a>
							</div>
							<div class="offer-info">
								<span class="main">{if $request_segment.id==1}ПРОДАЖА{else}SALE{/if}</span>
								{if !empty($item.kolichestvo_spalen)}&nbsp;•&nbsp; <strong>{if $request_segment.id==1}{$item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}{else}{$item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}{/if}</strong>{/if}
								{if !empty($special_offer.foreign_price)}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i> {$special_offer.foreign_price|price_format} {$special_offer.foreign_currency}</strong>
								{elseif !empty($special_offer.price_variant)}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i> {$special_offer.price_variant}</strong>
								{else}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i> {if $request_segment.id==1}Цена договорная{else}Negotiated price{/if}</strong>
								{/if}
							</div>
							<div class="offer-buttons">
								<a href="{$item->getUrl()}" class="offer-btn a-btn-green">{if $request_segment.id==1}Узнать больше{else}Learn more{/if}</a>
								<a href="{if $request_segment.id!=1}/en{/if}/feedback/request/?id[]=i{$item.id}" class="offer-btn a-btn-green-light"><i class="i-order"></i>{if $request_segment.id==1}Оставить заявку{else}Submit a request{/if}</a>
								<div class="right-btn a-right">
									<a href="{$url_prefix}/catalog/favorites/" data-id="{$special_offer.id}" class="btn-favorites to-favorites{if !empty($favorites_vars[$special_offer.id])} in-favorites{/if}" title="Добавить в избранное"><i></i></a>
									<a href="{$url_prefix}/catalog/compare/" data-id="{$special_offer.id}" class="btn-compare to-compare{if !empty($compare_vars[$special_offer.id])} in-compare{/if}" title="Добавить к сравнению"><i></i></a>
								</div>
							</div>
						</div>
					{/capture}
					<div class="offer" data-title="{$item.title}" data-link="{$item->getUrl()}" data-info="{$offerInfo}"></div>
				{/if}
			</div>
		{/if}
    {/foreach}
{/if}