{if !empty($items)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
	{?$gravity = array(
		'TL' => 'top left',
		'T' => 'top center',
		'TR' => 'top right',
		'L' => 'left center',
		'C' => 'center center',
		'R' => 'right center',
		'BL' => 'bottom left',
		'B' => 'bottom center',
		'BR' => 'bottom right',
	)}
	<div class="quick-controls">
		<div class="flat-arrow m-prev" title="{$lang->get('Следующая квартира', 'Next apartment')}">{fetch file=$path . "arrow.svg"}</div>
		<div class="flat-arrow m-next" title="{$lang->get('Предыдущая квартира', 'Previous apartment')}">{fetch file=$path . "arrow.svg"}</div>
	</div>
	<div class="quick-items-cont">
		{foreach from=$items item=item}
			{if empty($quick_view_current_id)}{?$quick_view_current_id = $item.id}{/if}
			<div class="quick-item flat-wrap item-{$item.id}{if $item.id == $quick_view_current_id} m-current{/if}">
				<div class="close-quickview">{fetch file=$path . "close.svg"}</div>
				<div class="quick-title">
					{$item.title} 
					<span class="favorite-btn{if $item.in_favorites} m-added{/if} a-inline-block" data-id="{$item.id}" data-url="arenda">{fetch file=$path . "favorite.svg"}</span>
				</div>
				<div class="row-top a-justify">
					<div class="col1">
						{if !empty($item.price)}
							<div class="price">{substr($item.price,0,strpos($item.price,' '))} тыс руб.</div>
							<div class="price_per_m">За месяц</div>
						{else}
							<div class="price">{$lang->get('Цена по запросу', 'Price on request')}</div>
						{/if}
					</div>
					<div class="col2">
						{if is_array($item.icon)}
							{foreach from=$item.icon item=icon}
								<div class="skew m-sand-skew">{$icon}</div>
								{break}
							{/foreach}
						{else}
							<div class="skew m-sand-skew">{$item.icon}</div>
						{/if}
					</div>
				</div>
				<div class="row-bottom a-justify">
					<div class="col1">
						<div class="col-inner tabs-cont">
							{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
							{?$schemes = !empty($item.shemes) ? $item.shemes->getImages() : null}
							<div class="tabs-header a-inline-cont a-center">
								{if !empty($gallery)}
									<div class="tab-title m-current" data-target=".tab-photo">{$lang->get('ФОТО','PHOTO')}</div>
								{/if}
								{if !empty($item.address_coords)}
									<span class="slash"></span>
									<div class="tab-title" data-target=".tab-map">{$lang->get('КАРТА', 'MAP')}</div>
								{/if}
								{if !empty($schemes)}
									<span class="slash"></span>
									<div class="tab-title" data-target=".tab-scheme">{$lang->get('ПЛАНЫ', 'PLANS')}</div>
								{/if}
								{if ($request_segment.key == 'ru' && !empty($site_config.apartment_pdf_cover_ru)) || (!empty($site_config.apartment_pdf_cover_en) && $request_segment.key != 'ru')}
									<span class="slash"></span>
									<a rel="nofollow" target="_blank" href="{if $request_segment.key == 'ru' && !empty($site_config.apartment_pdf_cover_ru)}{$site_config.apartment_pdf_cover_ru->getUrl()}{elseif !empty($site_config.apartment_pdf_cover_en)}{$site_config.company_pdf_en->getUrl()}{/if}" class="tab-title-link">PDF</a>
								{/if}
							</div>
							<div class="tabs-pages">
								{if !empty($gallery)}
									<div class="tab-page tab-gallery tab-photo">
										<div class="img-wrap">
											<div class="swiper-container gallery-thumbs">
												<div class="swiper-wrapper">
													{foreach from=$gallery item=img name=img_n}
														<div class="swiper-slide{if $smarty.foreach.img_n.first} m-current{/if}">
															{if !empty($img->getUrl())}
																<img src="{$img->getUrl(120, 100, true)}" alt="">
															{/if}
														</div>
													{/foreach}
												</div>
											</div>
											<div class="swiper-container gallery-bot">
												<div class="swiper-wrapper">
													{foreach from=$gallery item=img name=img_n}
														{if !empty($img->getUrl())}
														<div class="swiper-slide open-photo" data-id="{$img.id}">
															<div class="cover" style="background:url({$img->getUrl(855,620)}){if !empty($gravity)} {$gravity[$img.gravity]}{/if}; background-size:cover;"></div>
														</div>
														{/if}
													{/foreach}
												</div>
											</div>
											<div class="nav">
												<div class="pagin">1 / 1</div>
												<div class="swiper-pagination"></div>
												<div class="swiper-button-next{if !empty($smarty.foreach.img_n.total) && $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
												<div class="swiper-button-prev{if !empty($smarty.foreach.img_n.total) && $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
											</div>
										</div>
									</div>
								{/if}
								{if !empty($item.address_coords)}
									<div class="tab-page tab-map">
										<div class="map" data-coords="{$item.address_coords}" data-title="{$item.title}"></div>
									</div>
								{/if}
								{if !empty($schemes)}
									<div class="tab-page tab-gallery tab-scheme">
										<div class="img-wrap">
											<div class="swiper-container gallery-thumbs">
												<div class="swiper-wrapper">
													{foreach from=$schemes item=img name=img_n}
														<div class="swiper-slide{if $smarty.foreach.img_n.first} m-current{/if}">
															{if !empty($img->getUrl())}
																<img src="{$img->getUrl(120, 100, true, false, array('gray', 'brit|0'))})}" alt="">
															{/if}
														</div>
													{/foreach}
												</div>
											</div>
											<div class="swiper-container gallery-bot">
												<div class="swiper-wrapper">
													{foreach from=$schemes item=img name=img_n}
														{if !empty($img->getUrl())}
														<div class="swiper-slide" data-id="{$img.id}">
															<a href="{!empty($url) ? $url : ''}" class="cover" style="background:url({$img->getUrl(855,620, false, false, array('gray', 'brit|0'))}){if !empty($gravity)} {$gravity[$img.gravity]}{/if}; background-size:contain;"></a>
														</div>
														{/if}
													{/foreach}
												</div>
											</div>
											<div class="nav">
												<div class="pagin">1 / 1</div>
												<div class="swiper-pagination"></div>
												<div class="swiper-button-next{if !empty($smarty.foreach.img_n.total) && $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
												<div class="swiper-button-prev{if !empty($smarty.foreach.img_n.total) && $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
											</div>
										</div>
									</div>
								{/if}
							</div>
						</div>
					</div>
					<div class="col2">
						{if !empty($item.features)}
							<div class="item-features">
								{foreach from=$item.features item=feature name=features}
									{if iteration>1} / {/if}{$feature}
								{/foreach}
							</div>
						{/if}
						{if !empty($type_properties)}
							<div class="opt-table">
								{foreach from=$type_properties item=prop}
									{if !empty($item[$prop.key])}
										{?$prop_val = ''}
										{if $prop.set == 1}
											{foreach from=$item[$prop.key] item=val}
												{if !empty($val['title']) || !empty($val['variant_title'])}
													{if !empty($prop_val)}{?$prop_val .= ', '}{/if}
													{?$prop_val .= !empty($val['variant_title'])? $val['variant_title'] : $val['title']}
												{elseif !is_array($val) && !is_object($val)}
													{if !empty($prop_val)}{?$prop_val .= ', '}{/if}
													{?$prop_val .= $val}
												{/if}
											{/foreach}
										{elseif !empty($item[$prop.key]['title']) || !empty($item[$prop.key]['variant_title'])}
											{?$prop_val = !empty($item[$prop.key]['variant_title'])? $item[$prop.key]['variant_title'] : $item[$prop.key]['title']}
										{elseif !is_array($item[$prop.key]) && !is_object($item[$prop.key])}
											{?$prop_val = $item[$prop.key]}
										{/if}
										{if !empty($prop_val)}
											<div class="opt-row">
												<div class="opt-col">{$prop.title}</div>
												<div class="opt-col">{$prop_val}</div>
											</div>
										{/if}
									{/if}		
								{/foreach}
							</div>
						{/if}
						<div class="opt-table buttons">
							<div class="opt-row">
								<div class="opt-col">
									<a href="{$item->getUrl($request_segment['id'])}" class="btn m-sand">{$lang->get('В деталях', 'In detail')}</a>
								</div>
								<div class="opt-col">
									<a href="{$url_prefix}/arenda/request/?id={$item.id}" class="btn m-magenta-fill">{$lang->get('Оставить заявку', 'Send your request')}</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
{/if}
