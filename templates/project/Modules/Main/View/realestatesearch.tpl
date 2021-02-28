{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
{/if}	
<div class="items-list">
		{foreach from=$items item=item name=name_n}
			{?$delim = ldelim . "!" . rdelim}
			{?$title = $item.title|replace:$delim:' '}
			{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
			{?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
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
							{foreach from=$gallery item=img name=img_n}
								<div class="swiper-slide">
									{if !empty($img->getUrl())}
										<a href="{!empty($url) ? $url : ''}"><img src="{$img->getUrl(372,270,true)}" alt=""></a>
									{/if}
								</div>
							{/foreach}
						</div>
					</div>

					<div class="cover-controls{if $smarty.foreach.img_n.total < 2} a-hidden{/if}">
						<div class="prev swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
						<div class="next swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
					</div>
				</div>
				<div class="item-offers">
					<div class="main">квартиры в продаже</div>
					<div class="offers-row">
						<div class="offers-col">
							{section loop=5 name=bedroom_count}
								{if iteration == 4}
									</div>
									<div class="offers-col">
								{/if}
								{if !empty($bedroom_count_filters[$item.id][iteration])}
									{?$bedroom = $bedroom_count_filters[$item.id][iteration]}
									<a href="{$item->getUrl()}apartments/?bed_number[]={$bedroom['bedroom_count']}" class="a-inline-cont">
										<span class="num">{$bedroom['bedroom_count']}{$bedroom['bedroom_count'] == 5 ? ' +' : ''}</span>
										<span class="slash"></span>
										<span>{($bedroom.area_min != $bedroom.area_max ? round($bedroom.area_min).'—'.round($bedroom.area_max) : round($bedroom.area_max)) . ($request_segment.key == 'ru' ? ' м²' : ' m²')|html}</span>
									</a>
								{else}
									<div class="a-inline-cont">
										<span class="num">{iteration}{iteration == 5 ? ' +' : ''}</span>
										<span class="slash"></span>
										<span class="empty">По запросу</span>
									</div>
								{/if}
							{/section}
						</div>
					</div>
				</div>
				<div class="item-params">
					<div class="main">Жилой комплекс </div>
					<div class="title"><span>{$title}</span></div>
					<div class="descr">{if !empty($item.district.prepositional)}{$item.district.prepositional}{/if}</div>
					{if !empty($item.properties.price_meter_from.value)}
						<div class="area">{$item.properties.price_meter_from.value}<i>+</i></div>
						<div class="small-descr">{$lang->get('тыс.рублей за м', 'ths rub. per m')}²</div>
					{/if}
					{if !empty($url)}
						<div class="bottom"><a href="{$url}" class="btn m-sand">В деталях</a></div>
					{/if}
				</div>
			</div>
		{/foreach}
	</div>
	{if !empty($count) && $count > count($items)}
		{?$items_rest = $count - count($items)}
		{if $items_rest > count($items)}{?$items_rest = count($items)}{/if}
		<div class="more-row a-center">
			<div class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if} data-url="/realestatesearch/"{if !empty($smarty.get.phrase)} data-phrase="{$smarty.get.phrase}{/if}">
				Показать еще {$items_rest|plural_form:'объект':'объекта':'объектов'}
			</div>
		</div>
	{/if}