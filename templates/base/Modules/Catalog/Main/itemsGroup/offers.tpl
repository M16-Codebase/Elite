<div class="catalog-inner">
	{if !empty($catalog_items)}
		<ul class="catalog-items-list">
			{foreach from=$catalog_items item=offer name=offer_items}
				{?$offer_title = !empty($offer.variant_title) ? $offer.variant_title|trim : 'No title'}
				{?$offer_item = $offer->getItem()}
				<li class="catalog-item-offer justify">
					<div class="offer-info">
						<div class="item-title"><a href="{$offer->getUrl()}#offer-{$offer.id}">{$offer_title}</a></div><br />
						<span class="main">{if $type_rent}{if $request_segment.id==1}АРЕНДА{else}Rent{/if}{else}{if $request_segment.id==1}ПРОДАЖА{else}Sale{/if}{/if}</span> 
						{if !empty($offer.ploschad_ot_offer)}
							&nbsp;•&nbsp; <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$offer.ploschad_ot_offer value_max=$offer.ploschad_do_offer value_range=$offer.ploschad_range}</strong>
						{/if}
					</div>
					<div class="object-info">
						{?$cover = $offer_item.gallery->getCover()}
						{if empty($cover)}
							{?$cover = $offer_item.gallery->getDefault()}
						{/if}
						{if !empty($cover)}
							<a href="{$offer_item->getUrl()}" class="cover">
								<img src="{$cover->getUrl(58,58,true)}" alt="{$offer_item.title}" />
							</a>
						{/if}						
						<div class="item-title"><a href="{$offer_item->getUrl()}">{$offer_item.title}</a></div>
						<div class="small-descr">
							{?$first_prop=true}
							{if !empty($offer_item.city)}{?$first_prop=false}{$offer_item.city}{/if}
							{if !empty($offer_item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$offer_item.district}{/if}
							{if !empty($offer_item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$offer_item.adres}{/if}
						</div>
					</div>
					<div class="offer-price">
						{if !empty($offer.price_variant)}
							{$offer.price_variant}
						{else}
							{if $request_segment.id==1} Цена договорная{else} Negotiated price{/if}
						{/if}
						{if !empty($offer.bez_komissii_offer) && $offer.bez_komissii_offer != 'Нет'}
							<div class="pink">{if $request_segment.id==1}Без комиссии{else}No commission{/if}</div>
						{/if}
					</div>
					<div class="offer-buttons">
						<div class="small-btn">
							<a href="{$url_prefix}/catalog/favorites/" data-id="{$offer.id}" class="btn-favorites to-favorites{if !empty($favorites_vars[$offer.id])} in-favorites{/if}" title="Добавить в избранное"><i></i></a>
							<a href="{$url_prefix}/catalog/compare/" class="btn-compare to-compare{if !empty($compare_vars[$offer.id])} in-compare{/if}" data-id="{$offer.id}" title="Добавить к сравнению"><i></i></a>
						</div>
						<a href="{$url_prefix}/feedback/request/?id[]={$offer.id}" title="Оставить заявку" class="big-btn"><i></i></a>
					</div>
				</li>
			{/foreach}
		</ul>
		{include file="components/paging.tpl" show=4}
	{else}
		<div class="empty-result">{if $request_segment.id==1}По вашему запросу ничего не найдено.{else}Sorry, no results found.{/if}</div>
	{/if}
</div>