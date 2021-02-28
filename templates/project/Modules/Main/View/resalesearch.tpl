{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
{/if}
{if !empty($smarty.get.phrase)}
	{if !empty($items)}
	<div class="list-result">
		<div class="items-list">
			{foreach from=$items item=item name=name_n}
				{?$delim = ldelim . "!" . rdelim}
				{?$title = $item.title|replace:$delim:' '}
				{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
				{?$url = !empty($item->getUrl()) ? ($item->getUrl() . '?current_id=' . $item.id) : null}
				<div class="item-wrap">
					<div class="item-cover">
						<div class="markers">
							{foreach from=$item.icon item=icon}
								<div class="skew m-sand-skew">{$icon}</div>
								{break}
							{/foreach}
						</div>
						<div class="swiper-container cover-slider">
							<div class="w4 swiper-wrapper">
								{foreach from=$gallery item=img name=img_resale_n}
									<div class="swiper-slide">
										{if !empty($img)}
											<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url({$img->getUrl(372,270)}){if !empty($gravity)}{if !empty($gravity)} {$gravity[$img.gravity]}{/if}{/if}; background-size:cover;">
											</a>
										{/if}
									</div>
								{/foreach}
							</div>
						</div>

						<div class="cover-controls{if $smarty.foreach.img_resale_n.total < 2} a-hidden{/if}">
							<div class="prev swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
							<div class="next swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
						</div>
					</div>
					<div class="item-offers">
						<div class="main">Расположение</div>
						{if !empty($item.address)}<div class="title"><span>{$item.address}</span></div>{/if}
						{if !empty($item.district.prepositional)}<div class="descr">{$item.district.prepositional}</div>{/if}
						{if !empty($item.price)}
							<div class="price">
								<span class="marker">цена</span>
								<div class="full_price">{$item.properties.price.value} {$lang->get('млн руб.', 'mln rub.')}</div>
								<div class="price_per_m">{if !empty($item.properties.area_all.value)}{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м', 'ths rub. per m')}<sup>2</sup>{/if}</div>
							</div>
						{/if}
					</div>
					<div class="item-params flat-wrap">
						<div class="main">Квартира</div>
						<div class="title"><span>{$item.bed_number|plural_form:'спальня':'спальни':'спален'}</span></div>
						{?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
						{if !empty($item.wc_number)}{?$wc_number = $item.wc_number|plural_form:'санузел':'санузла':'санузлов'}{/if}
						<div class="descr">
							{if !empty($floors)}{$floors[0]} уровня{/if}{if !empty($wc_number) && !empty($floors)}<span>•</span>{/if}
							{!empty($wc_number) ? $wc_number : ''}
							{if !empty($wc_number) && !empty($item.floor)}<span>•</span>{/if}{if !empty($item.floor)}Этаж {$item.floor} {if $item.number_storeys}из {$item.number_storeys}{/if}{/if}
						</div>
						{if !empty($item.properties.area_all.value)}
							<div class="area"><i>~</i>{$item.properties.area_all.value|round}<span>м<sup>2</sup></span></div>
						{/if}
						{if !empty($item.overhang)}
							<div class="small-descr">
								Есть
								{foreach from=$item.overhang item=overhang name=overhang_n}
									{$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
								{/foreach}
							</div>
						{/if}
						{if !empty($url)}
							<div class="bottom"><a href="{$url}" class="btn m-sand">В деталях</a></div>
						{/if}
						<div class="favorite{if $item.in_favorites} m-added{/if}" {if !empty($item.id) && !empty($moduleUrl)}data-id="{$item.id}" data-url="{$moduleUrl}"{/if}>{fetch file=$path . "favorite.svg"}</div>
					</div>
				</div>
			{/foreach}
		</div>
		{if !empty($count) && $count > count($items)}
			{?$items_rest = $count - count($items)}
			{if $items_rest > count($items)}{?$items_rest = count($items)}{/if}
			<div class="more-row a-center">
				<div class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
					Показать еще {$items_rest|plural_form:'объект':'объекта':'объектов'}
				</div>
			</div>
		{/if}
	</div>	
	{/if}
{/if}
<div class="resale m-center">
	<div class="descr">{$lang->get('А почему бы не изучить предложения в готовых домах?', 'Why not check some apartments fo resale?')}</div>
	<a href="{$url_prefix}/resale/" class="btn m-light-magenta">Результаты поиска в каталоге вторичной недвижимости</a>
</div>