<div class="content-block selected-page">
	<div class="page-title">
		{include file="components/breadcrumb.tpl"}
		<h1>{if $request_segment.id==1}Избранные предложения{else}Selected offers{/if}</h1>
		<div class="header-print">
			<a href="#" class="delete-all a-pink clear-favorites"><i></i>Очистить список</a>		
			{*<a href="#" class="print-link"><i></i>Версия для печати</a>*}
		</div>
	</div>
	<div class="catalog-block selected-block main-col">
		<div class="catalog-header selected-header">
			<div class="filter-check a-left">
				<input type="checkbox" class="cbx check-all">
			</div>
			<span class="group-offer"><i></i>Предложения <span>{$favorites.counts.variants}</span></span>
			{if !empty($favorites.items)}
				<a href="{$url_prefix}/feedback/request/" class="make-order a-right">Оставить заявку на отмеченные предложения —<span class="icon-cont"><i class="i-order"></i></span></a>
			{/if}
		</div>
		<form method="POST" class="offer-selection-form">
			<div class="offer-body">
				<div class="catalog-inner">
					{if !empty($favorites.items)}
						{foreach from=$favorites.items item=f_data}
							{?$item = $f_data.item}
							{?$type = $item->getType()}
							{?$special_offer = $item['special_variant']}
							{if empty($f_data_type) || $f_data_type != $item.type_id}
								{if !empty($f_data_group)}</ul>{/if}
								<div class="catalog-type-title type-{$item.type_id}">
									<a href="{$type->getUrl()}" class="title more-arrow">
										{if $item.type_id == 59 || $item.type_id == 61 || $item.type_id == 62}
											{$type.title}
										{else}
											{?$parent = $type->getParent()}
											{$parent.title} 
											{if in_array($item.type_id, array('63', '65', '66'))}
												{$lang->get('в аренду', 'for rent')}
											{else}
												{$lang->get('на продажу', 'for sale')}
											{/if}
										{/if}
									</a>
									<div class="added">Добавлено</div>
								</div>
								<ul class="catalog-items-list">
							{/if}
							{foreach from=$f_data.variants item=offer}							
								<li class="catalog-item-offer justify v{$offer.id}">
									<div class="cbx-col a-left link-except">
										<input type="checkbox" name="items[]" value="{$offer.id}" class="select-offer cbx">
									</div>
									<div class="offer-info">
										{if $item.type_id == 62}
											<div class="item-title"><a href="{$item->getUrl()}">{$item.title}</a></div><br>
										{else}
											<div class="item-title"><a href="{$offer->getUrl()}">{$offer.variant_title}</a></div><br>
										{/if}
										<span class="main">
											{if in_array($item.type_id, array('63', '65', '66'))}
												{$lang->get('Аренда', 'Rent')}
											{else}
												{$lang->get('Продажа', 'Sale')}
											{/if}
										</span> 
										{if $item.type_id == 62}
											{if !empty($item.kolichestvo_spalen)}
												&nbsp;•&nbsp; <strong>
													{if $ru}
														{$item.kolichestvo_spalen|plural_form:'спальня':'спальни':'спален'}
													{else}
														{$item.kolichestvo_spalen|plural_form:'bedroom':'bedrooms':'bedrooms'}
													{/if}
												</strong>
											{/if}
										{else}
											{if !empty($special_offer.ploschad_ot_offer)}
												&nbsp;•&nbsp; <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$special_offer.ploschad_ot_offer value_max=$special_offer.ploschad_do_offer value_range=$special_offer.ploschad_range}</strong> 
											{/if}
										{/if}
									</div>
									<div class="object-info">
										{if $item.type_id == 62}
											{?$images = $item.gallery->getImages()}
											{if !empty($images)}
												{foreach from=$images item=$img name=appt_images}
													{if iteration <= 4}
														<a href="{$item->getUrl()}" class="cover">
															<img src="{$img->getUrl(58,58,true)}" alt="{$item.title}" />
														</a>
													{/if}
												{/foreach}
											{/if}
										{else}
											{?$cover = $item.gallery->getCover()}
											{if empty($cover)}
												{?$cover = $item.gallery->getDefault()}
											{/if}
											{if !empty($cover)}
												<a href="{$item->getUrl()}" class="cover">
													<img src="{$cover->getUrl(58,58,true)}" alt="{$item.title}" />
												</a>
											{/if}
											<div class="item-title"><a href="/ru/catalog/54/63/i50/">{$item.title}</a>
												{if !empty($item.klass)}
													&nbsp;<span class="item-class">
														{if $item.klass =='Без класса'}
															{$item.klass}
														{else}
															{if $ru}Класс{else}Class{/if} {$item.klass}
														{/if}
													</span>
												{/if}
											</div>											
											<div class="small-descr">
												{?$first_prop=true}
												{if !empty($item.city)}{?$first_prop=false}{$item.city}{/if}
												{if !empty($item.district)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.district}{/if}
												{if !empty($item.adres)}{if !$first_prop}, {else}{?$first_prop=false}{/if}{$item.adres}{/if}
											</div>
										{/if}
									</div>
									<div class="offer-price">
										{if $item.type_id == 62}
											{if !empty($special_offer) && !empty($special_offer.foreign_price)}	
												{if !empty($special_offer.foreign_price)}
													{if !empty($special_offer.foreign_currency)}{$special_offer.foreign_currency} {/if}{$special_offer.foreign_price|price_format}
												{/if}
											{else}
												{$lang->get('Цена договорная', 'Negotiated price')}
											{/if}
										{else}
											{if !empty($item.price_open_min)} 
												{$item.price_open_range|price_format_range}
											{elseif !empty($special_offer.price_variant)}
												{$special_offer.price_variant}
											{else}
												{$lang->get('Цена договорная', 'Negotiated price')}
											{/if}
										{/if}
										{if !empty($offer.bez_komissii_offer) && $offer.bez_komissii_offer != 'Нет'}
											<div class="pink">{if $ru}Без комиссии{else}No commission{/if}</div>
										{/if}
									</div>
									<div class="offer-added">
										{if $item.type_id == 61 || $item.type_id == 62}
											{?$offer_id = $special_offer.id}
										{else}
											{?$offer_id = $offer.id}										
										{/if}
										{?$added = floor((time() - $favorites.data[$offer_id])/(60*60*24))}	
										{if $added == 0}{$lang->get('Сегодня', 'Today')}
										{elseif $added == 1}{$lang->get('Вчера', 'Yesterday')}
										{else}
											{$favorites.data[$offer_id]|date_format:'%d.%m.%Y'}
										{/if}
									</div>
									<div class="offer-buttons">
										<div class="small-btn">
											<a href="{$url_prefix}/catalog/favorites/" class="btn-favorites to-favorites in-favorites" data-id="{$offer.id}" title="Убрать из избранного"><i></i></a>
											<a href="{$url_prefix}/catalog/compare/" class="btn-compare to-compare{if !empty($compare_vars[$offer.id])} in-compare{/if}" data-id="{$offer.id}" title="Добавить к сравнению"><i></i></a>
										</div>
										<a href="{$url_prefix}/feedback/request/?id[]={$offer.id}" class="big-btn"><i></i></a>
									</div>
								</li>
							{/foreach}
							{?$f_data_type = $item.type_id}
						{/foreach}					
						</ul>
					{else}
						<div class="empty-favs">
							Список избранных предложений пуст.
						</div>
					{/if}
				</div>			
			</div>
		</form>
	</div>
</div>