<tr class="tr-border">
	<td>ПРЕДЛОЖЕНИЯ</td>
	{section loop=count($items) name=compare_loop}
		<td></td>
	{/section}
</tr>
<tr class="tr-offers offers-list">
	<td></td>
	{foreach from=$items item=item_group}
		{?$item = $item_group.item}
		<td class="i{$item.id}" data-id="{$item.id}">
			<ul class="catalog-items-list">
				{foreach from=$item_group.var_ids item=id name=variants_list}
					{?$offer = $variants[$id]}
					<li class="catalog-item-offer v{$offer.id} slide-box{if first} m-open{/if}" data-id="{$offer.id}">
						<div class="slide-header">
							<div class="item-title more-arrow">
								<div class="a-inline-block box-except"><input type="checkbox" value="{$offer.id}" class="cbx select-offer"></div>
								{$offer.variant_title}
							</div>
							<div class="item-info">
								<span class="main">
									{if in_array($item.type_id, array('63', '65', '66'))}
										{$lang->get('Аренда', 'Rent')}
									{else}
										{$lang->get('Продажа', 'Sale')}
									{/if}
								</span>
								{if !empty($offer.ploschad_ot_offer)}
									&nbsp;•&nbsp; <strong>{include file="Admin/components/view_entity/value_range_view.tpl" value_min=$offer.ploschad_ot_offer value_max=$offer.ploschad_do_offer value_range=$offer.ploschad_range}</strong>
								{/if}
								&nbsp;•&nbsp; <strong><i class="i-wallet"></i>
								{if !empty($offer.price_variant)}
									{$offer.price_variant}
								{else}
									{if $request_segment.id==1} Цена договорная{else} Negotiated price{/if}
								{/if}</strong>
							</div>
						</div>
						<div class="slide-body">
							<div class="justify">
								<div class="col1">
									<div class="offer-photos">
										{?$images = $offer.gallery->getImages()}
										{if !empty($images)}
											{foreach from=$images item=img name=offer_images}
												{if first}
													<div class="cover fancybox" data-fancybox-href="{$img->getUrl()}" rel="offer-gal-{$offer.id}">
														<img src="{$img->getUrl(58,58,true)}" alt="{$offer.variant_title}">
														<div class="cover-title">{$lang->get('Фото', 'Photo')}<span class="num">{count($images)}</span></div>
													</div>
												{else}
													<div class="fancybox a-hidden" data-fancybox-href="{$img->getUrl()}" rel="offer-gal-{$offer.id}">
														<img src="{$img->getUrl(58,58,true)}" alt="{$offer.variant_title}">
													</div>
												{/if}				
											{/foreach}
										{else}
											<div class="cover">																
												<img src="/img/icons/default_cover.png" alt="{$offer.variant_title}">																
												<div class="cover-title">{$lang->get('Фото', 'Photo')}<span class="num">0</span></div>
											</div>
										{/if}
									</div>
									<div class="offer-schemes">
										{?$images = $offer.scheme->getImages()}
										{if !empty($images)}
											{foreach from=$images item=img name=offer_images}
												{if first}
													<div class="cover fancybox" data-fancybox-href="{$img->getUrl()}" rel="offer-sch-{$offer.id}">
														<img src="{$img->getUrl(58,58,true)}" alt="{$offer.variant_title}">
														<div class="cover-title">{$lang->get('Планы', 'Scheme')}<span class="num">{count($images)}</span></div>
													</div>
												{else}
													<div class="fancybox a-hidden" data-fancybox-href="{$img->getUrl()}" rel="offer-sch-{$offer.id}">
														<img src="{$img->getUrl(58,58,true)}" alt="{$offer.variant_title}">
													</div>
												{/if}
											{/foreach}
										{else}
											<div class="cover">
												<img src="/img/icons/scheme.png" alt="{$offer.variant_title}">
												<div class="cover-title">{$lang->get('Планы', 'Scheme')}<span class="num">0</span></div>
											</div>
										{/if}
									</div>
									<div class="offer-buttons">
										<a href="{$url_prefix}/feedback/request/?id[]={$offer.id}" title="Оставить заявку" class="big-btn"><i></i></a>
										<div class="small-btn">
											<a href="/catalog-view/addToFavorites/" data-id="{$offer.id}" class="btn-favorites to-favorites{if !empty($favorites_vars[$offer.id])} in-favorites{/if}" title="Добавить в избранное"><i></i></a>
											<a href="#" class="btn-compare to-compare in-compare" data-id="{$offer.id}" title="Убрать из сравнения"><i></i></a>
										</div>
									</div>
								</div>
								<div class="col3">
									<div class="properties-list">
										{foreach from=$properties item=$offer_prop name=offer_props}
											{if $offer_prop.key!='kommercheskie_uslovija' && $offer_prop.key!='tip_arendnoj_stavki_offer' && $offer_prop.key!='arendnaja_stavka_ne_vkljuchaet_offer' && $offer_prop.key!='arendnaja_stavka_vkljuchaet_offer' && $offer_prop.key!='price_variant' && $offer_prop.key!='bez_komissii_offer' && isset($offer['properties'][$offer_prop.key]['real_value']) && $offer['properties'][$offer_prop.key]['real_value'] != ''}
												<div class="prop-cont">
													{include file ="Modules/Catalog/Main/variantsProperties.tpl" item_prop=$offer_prop catalog_item=$offer}
												</div>
											{/if}
										{/foreach}
										{if !empty($offer.bez_komissii_offer) && ($offer.bez_komissii_offer=='Да' || $offer.bez_komissii_offer=='Yes')}
											<div class="prop-cont">
												<div class="prop-title pink">Без комиссии</div>
											</div>
										{/if}
										{if !empty($offer.arendnaja_stavka_vkljuchaet_offer) || !empty($offer.arendnaja_stavka_ne_vkljuchaet_offer) || !empty($offer.kommercheskie_uslovija) || !empty($offer.tip_arendnoj_stavki_offer)}
											{foreach from=$properties item=$offer_prop name=offer_props}
												{if ($offer_prop.key=='arendnaja_stavka_ne_vkljuchaet_offer' || $offer_prop.key=='arendnaja_stavka_vkljuchaet_offer' || $offer_prop.key=='kommercheskie_uslovija' || $offer_prop.key=='tip_arendnoj_stavki_offer') && isset($offer['properties'][$offer_prop.key]['real_value']) && $offer['properties'][$offer_prop.key]['real_value'] != ''}
													<div class="prop-cont">
														{include file ="Modules/Catalog/Main/variantsProperties.tpl" item_prop=$offer_prop catalog_item=$offer}
													</div>
												{/if}
											{/foreach}
										{/if}
									</div>
								</div>
								<div class="semicircle-btn slide-header">
									<div class="semicircle-btn-cont btn">
										<i class="i-arrow"></i>
									</div>
								</div>
							</div>
						</div>
					</li>
				{/foreach}
			</ul>
		</td>
	{/foreach}
</tr>