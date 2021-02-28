<form action="#" class="m-hidden sendQueryInfo"><input type="hidden" name="query_string" value="{$quicky.server['QUERY_STRING']}&from_filter" /></form>
<div class="catalog-inner">
	{if !empty($catalog_items)}
		<ul class="catalog-items-list">
			{foreach from=$catalog_items item=item}
				{?$item_title = !empty($item.title)? $item.title|trim : 'No title'}
				{?$special_offer = NULL;}{*$item['special_variant']*}
				<li class="catalog-item-object justify" data-id="{$item.id}">
					{if !empty($item.jarlyk)}
						{if $item.jarlyk=='Эксклюзив' || $item.jarlyk=='Exclusive'}
							{?$itemTag='exclusive'}
						{elseif $item.jarlyk=='Новый' || $item.jarlyk=='Новый объект' || $item.jarlyk=='Новое предложение' || $item.jarlyk=='New'}
							{?$itemTag='new'}
						{elseif $item.jarlyk=='Последние помещения' || $item.jarlyk=='Последний участок' || $item.jarlyk=='Последняя квартира в здании'|| $item.jarlyk=='Last offers' || $item.jarlyk=='Last flat on offer'}
							{?$itemTag='last'}
						{/if}
						<div class="item-tag{if !empty($itemTag)} {$itemTag}{/if}">{$item.jarlyk}</div>
					{/if}
					{if !empty($item.gallery)}
						<a href="{$item->getUrl()}" class="object-cover">
							{?$cover = $item.gallery->getCover()}
							{if empty($cover)}
								{?$cover = $item.gallery->getDefault()}
							{/if}
							{if !empty($cover)}
								<img src="{$cover->getUrl(204,138,true)}" alt="{$item_title}" />
							{/if}
						</a>
					{/if}
					<div class="object-info">
						<div class="item-title">
							<a href="{$item->getUrl()}" class="more-arrow sendQueryOnClick">{$item_title}{if !empty($item.klass)}<span class="class">{if $request_segment.id==1}Класс{else}Class{/if} {$item.klass}</span>{/if}</a>
						</div>
						{if $type_apartments==true}
							<div class="item-info justify">
								<div class="big-col">
									<span class="main">{if $request_segment.id==1}ПРОДАЖА{else}SALE{/if}</span>  
									{if !empty($item.kolichestvo_spalen)}
										&nbsp;•&nbsp; <strong>
											{if $request_segment.id==1}
												{$item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}
											{else}
												{$item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}
											{/if}
										</strong> 
									{/if}
									{?$special_offer = $item['special_variant']}
									&nbsp;•&nbsp; <strong><i class="i-wallet"></i>	
									{if !empty($special_offer) && !empty($special_offer.foreign_price)}	
										{if !empty($special_offer.foreign_price)}
											{if !empty($special_offer.foreign_currency)}{$special_offer.foreign_currency} {/if}{$special_offer.foreign_price|price_format}
										{/if}
									{else}
										{if $request_segment.id==1} Цена договорная{else} Negotiated price{/if}
									{/if}
									</strong>
								</div>
								<div class="small-col">
									<span class="main">{if $request_segment.id==1}КОД{else}CODE{/if} — {$item.code}</span>
								</div>
							</div>
						{/if}
						<div class="item-info">
							{if $type_apartments!=true}
								<div class="">
									{?$first_prop=true}
									{if !empty($item.city)}{?$first_prop=false}{$item.city}{/if}
									{if !empty($item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.district}{/if}
									{if !empty($item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.adres}{/if}
								</div>
							{/if}
							{if !empty($item.properties.stantsii_metro) && $item.properties.stantsii_metro.value && !empty($metro_stations)}
								<ul class="metro a-inline-cont">
									{foreach from=$item.properties.stantsii_metro.value item=metro_id}
										{if !empty($metro_stations[$metro_id])}
											{?$metro = $metro_stations[$metro_id]}
											<li><i class="i-metro line-{$metro.line_id}"></i>{$metro.title[$request_segment.id]}{if !empty($item.metro_data[$metro_id].distance)} <span class="small-descr">— {$item.metro_data[$metro_id].distance} м</span>{/if}</li>
										{/if}
									{/foreach}
								</ul>
							{/if}
							{if $type_apartments==true && !empty($item.post.annotation)}
								<div class="small-descr">{$item.post.annotation}</div>
							{/if}
						</div>
						{if $type_apartments!=true}
							<div class="item-info justify">
								<div class="big-col">
									{if !empty($item['special_variant']) && $type_apartments!=true}
										{?$special_offer = $item['special_variant']}
										<span class="main">{if $type_rent}{if $request_segment.id==1}АРЕНДА{else}RENT{/if}{else}{if $request_segment.id==1}ПРОДАЖА{else}SALE{/if}{/if}</span>  
										{if !empty($special_offer.ploschad_ot_offer)}
											&nbsp;•&nbsp; <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}</strong> 
										{/if}
										&nbsp;•&nbsp; <strong><i class="i-wallet"></i> 
											{if !empty($item.price_open_min)} 
												{$item.price_open_range|price_format_range}
											{elseif !empty($special_offer.price_variant)}
												{$special_offer.price_variant}
											{else}
												{if $request_segment.id==1}Цена договорная{else}Negotiated price{/if}
											{/if}
										</strong>
									{/if}
								</div>
								<div class="small-col">
									<span class="main">{*if $request_segment.id==1}КОД{else}CODE{/if} — {$item.code*}</span>
								</div>
							</div>
						{/if}
						{?$offers = !empty($find_variants[$item['id']]) ? $find_variants[$item['id']] : NULL}
						{if !empty($offers)}
							<div class="offers-table-block slide-box">
								<div class="offers-table-cont slide-body a-hidden">
									<table class="offers-table">
										{foreach from=$offers item=offer name=object_offers}
											{?$offer_title = !empty($offer.variant_title) ? $offer.variant_title|trim : 'No title'}
											<tr{if iteration%2 == 0} class="even"{/if}>
												<td><a href="{$offer->getUrl()}#offer-{$offer.id}">{$offer_title}</a></td>
												<td class="td-price">
													{if !empty($offer.price_variant)}
														{$offer.price_variant}
													{else}
														{if $request_segment.id==1} Цена договорная{else} Negotiated price{/if}
													{/if}
												</td>
												<td class="small-col">
													<a href="/catalog-view/addToFavorites/" data-id="{$offer.id}" class="to-favorites{if in_array($offer.id, $favorites)} in-favorites{/if}"></a>
													{if !empty($offer.bez_komissii_offer) && $offer.bez_komissii_offer != 'Нет'}
														<span class="pink">{if $request_segment.id==1}Без комиссии{else}No commission{/if}</span>
													{/if}
												</td>
											</tr>
										{/foreach}
									</table>
								</div>
								<a href="{$item->getUrl()}#offer-{$offer.id}" class="more-offers a-inline-block slide-header m-stop">
									<i></i>
									<span class="cl-text">
									{if $request_segment.id==1}
										Показать {count($offers)|plural_form:'предложение':'предложения':'предложений'}
									{else}
										Show {count($offers)|plural_form:'offer':'offers':'offers'}
									{/if}
									</span>
									<span class="op-text">
										{if $request_segment.id==1}Скрыть{else}Hide{/if}
									</span>
								</a>
							</div>
						{/if}
						{if $type_apartments==true}
							<div class="offer-buttons">
								<a href="{$item->getUrl()}" class="offer-btn a-btn-green">{if $request_segment.id==1}Узнать больше{else}Learn more{/if}</a>
								<a href="{if $ru}/en{/if}/feedback/request/?id[]=i{$item.id}" class="offer-btn a-btn-green-light"><i class="i-order"></i>{if $request_segment.id==1}Оставить заявку{else}Submit a request{/if}</a>
								<div class="right-btn a-right">
									<a href="{$url_prefix}/catalog/favorites/" data-id="{$special_offer.id}" class="btn-favorites to-favorites{if !empty($favorites_vars[$special_offer.id])} in-favorites{/if}" title="Добавить в избранное"><i></i></a>
									<a href="{$url_prefix}/catalog/compare/" data-id="{$special_offer.id}" class="btn-compare to-compare{if !empty($compare_vars[$special_offer.id])} in-compare{/if}" title="Добавить к сравнению"><i></i></a>
								</div>
							</div>
						{/if}
					</div>
				</li>
			{/foreach}
			{if !empty($smarty.get.tip_objekta) && (!is_array($smarty.get.tip_objekta) || count($smarty.get.tip_objekta) == 1)}
				{?$relater_types = array(
					'323' => array(
						'url' => $main_types[55]['children'][67]->getUrl() . '?tip_kompleksa[]=88',
						'title' => $main_types[55]['info']['title']
					),
					'324' => array(
						'url' => $main_types[55]['children'][67]->getUrl() . '?tip_kompleksa[]=89',
						'title' => $main_types[55]['info']['title']
					),
					'325' => array(
						'url' => $main_types[55]['children'][67]->getUrl() . '?tip_kompleksa[]=614',
						'title' => $main_types[55]['info']['title']
					),
					'326' => array(
						'url' => $main_types[54]['children'][64]->getUrl(),
						'title' => $main_types[54]['info']['title']
					),
					'327' => array(
						'url' => $main_types[60]['children'][68]->getUrl(),
						'title' => $main_types[60]['info']['title']
					),
					'328' => array(
						'url' => $main_types[59]['info']->getUrl(),
						'title' => $main_types[59]['info']['title']
					)
				)}	
				{?$related_type_id = is_array($smarty.get.tip_objekta)? $smarty.get.tip_objekta[0] : $smarty.get.tip_objekta}
				{if !empty($relater_types[$related_type_id])}
					{?$relater_type = $relater_types[$related_type_id]}
					<li class="catalog-item-object related-type justify">
						<div class="small-col">{if $request_segment.id==1}Вас также может заинтересовать{else}You may also be interested{/if}</div>
						<div class="big-col">
							<a href="{$relater_type.url}" class="type-title more-arrow">{$relater_type.title} {if $ru}на продажу{else}for sale{/if}</a>
						</div>
					</li>
				{/if}
			{/if}
		</ul>
		{include file="components/paging.tpl" show=3}
	{else}
		<div class="empty-result">{if $request_segment.id==1}По вашему запросу ничего не найдено.{else}Sorry, no results found.{/if}</div>
	{/if}
</div>