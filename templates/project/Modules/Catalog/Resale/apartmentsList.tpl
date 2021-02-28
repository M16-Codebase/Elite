{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
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
{if empty($smarty.get.view) || $smarty.get.view != 'map'}
	{if !empty($items)}
		<div class="list-result">
			<div class="items-list">
				{foreach from=$items item=item name=name_n}
					{?$delim = ldelim . "!" . rdelim}
					{?$title = $item.title|replace:$delim:' '}
					{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
					{?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
					<a href="{if !empty($url)}{$url}{/if}" class="item-wrap">
						<div class="item-cover">
							<div class="markers">
								{if is_array($item.icon)}
									{foreach from=$item.icon item=icon}
										<div class="skew m-sand-skew">{$icon}</div>
										{break}
									{/foreach}
								{else}
									<div class="skew m-sand-skew">{$item.icon}</div>
								{/if}
							</div>
							<div class="swiper-container cover-slider">
								<div class="w4 swiper-wrapper">
									{foreach from=$gallery item=img name=img_n}
										<div class="swiper-slide">
											{if !empty($img)}
												<div{if iteration > 1} data-bg-img='{$img->getUrl(372,270)}'{/if} 
													class='cover{if iteration > 1} delay-img{/if}' 
													style="{if iteration < 2}background-image: url({$img->getUrl(372,270)});{/if}background-position:{if !empty($gravity)} {$gravity[$img.gravity]}{/if}; background-size:cover;"
												>
												</div>
{*												<div href="{if !empty($url)}{$url}{/if}" class='cover' style="background-image: url({$img->getUrl(372,270)});background-position:{if !empty($gravity)} {$gravity[$img.gravity]}{/if}; background-size:cover;"></div>*}
											{/if}
										</div>
									{/foreach}
								</div>
							</div>
							<div class="cover-controls">
								<div class="prev swiper-button-prev{if $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
								<div class="next swiper-button-next{if $smarty.foreach.img_n.total < 2} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
								<div class="quickview" data-id="{$item.id}">{$lang->get('Быстрый просмотр', 'Quick View')}</div>
							</div>
						</div>
						<div class="item-offers">
							{if !empty($item.address) || !empty($item.district.prepositional)}<div class="main">{$lang->get('Расположение', 'Location')}</div>{/if}
							{if !empty($item.address)}<div class="title"><span>{$item.address}</span></div>{/if}
							{if !empty($item.district.prepositional)}<div class="descr">{$item.district.prepositional}</div>{/if}
							{if !empty($item.price)}
								<div class="price">
									<span class="marker">{$lang->get('цена', 'Price')}</span>
									<div class="full_price">{$item.properties.price.value} {$lang->get('млн руб.', 'mln rub.')}</div>
									<div class="price_per_m">{if !empty($item.properties.area_all.value)}{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м', 'ths rub. per m')}<sup>2</sup>{/if}</div>
								</div>
							{/if}
						</div>
						<div class="item-params flat-wrap">
							<div class="main">{$item.typerk}</div>
							<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>
							{?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
							{if !empty($item.wc_number)}{?$wc_number = $lang->get($item.wc_number|plural_form:'санузел':'санузла':'санузлов', $item.wc_number|plural_form:'bathroom':'bathrooms':'bathrooms')}{/if}
							<div class="descr">
								{if !empty($floors)}{$floors[0]} {$lang->get('уровня', 'floors')}{/if}{if !empty($wc_number) && !empty($floors)}<span> • </span>{/if}
								{!empty($wc_number) ? $wc_number : ''}
								{if !empty($wc_number) && !empty($item.floor)}<span> • </span>{/if}{if !empty($item.floor)}{$lang->get('Этаж', 'Floor')} {$item.floor} {if $item.number_storeys}{$lang->get('из', 'of')} {$item.number_storeys}{/if}{/if}
							</div>
							{if !empty($item.properties.area_all.value)}
								<div class="area"><i>~</i>{$item.properties.area_all.value|round} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
							{/if}
							{if !empty($item.overhang)}
								<div class="small-descr">
									{$lang->get('Есть','With')}&nbsp;
                                    {if is_array($item.overhang)}
                                        {foreach from=$item.overhang item=overhang name=overhang_n}
                                            {$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
                                        {/foreach}
                                    {else}
                                        {$item.overhang}
                                    {/if}
								</div>
							{/if}
							{if !empty($url)}
								<div class="bottom"><div class="btn m-sand">{$lang->get('В деталях', 'IN DETAIL')}</div></div>
							{/if}
							<div class="favorite{if $item.in_favorites} m-added{/if}" {if !empty($item.id) && !empty($moduleUrl)} data-id="{$item.id}" data-url="{$moduleUrl}"{/if}>
								{fetch file=$path . "favorite.svg"}
                <span>
                  {fetch file=$path . "favorite.svg"}
                </span>
							</div>
						</div>
					</a>
				{/foreach}
			</div>
            
            
			{if empty($smarty.get.page)} 
                {? $page_num = 1 }
            {else} 
                {? $page_num = $smarty.get.page }
            {/if}
            
            {? $count_items = count($items)}
            {?$cur_count_items = $page_num * $pageSize}

            {if !empty($count) && $cur_count_items != $count}
                {?$items_rest = $count - $cur_count_items}
                {if $items_rest > $pageSize}{?$items_rest = $pageSize}{/if}
                {if $items_rest > 0 && $count_items >= $pageSize}
					<div class="more-row a-center">
						<div class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
                            {if $request_segment.key == 'ru'}
								Показать еще {$items_rest|plural_form:'предложение':'предложения':'предложений'}
                            {else}
								Show {$items_rest} more {$items_rest|plural_form:'offer':'offers':'offers':false}
                            {/if}
						</div>
					</div>
                {/if}
            {/if}
            
		</div>
	{else}
		<div class="list-result m-empty">
			<div class="items-list">
		<div class="resale m-gray">
			<div class="main">{$lang->get('К нашему великому сожалению', 'Unfortunatly')}</div>
			<div class="title"><span>{$lang->get('Вариантов с заданными параметрами не найдено', 'We couldn\'t find appropriate offers with parameters you\'ve set')}</span></div>
			<div class="descr">{$lang->get('А почему бы не изучить похожие предложения в строящихся домах?','Why not check some offers in buildings under construction?')}</div>
			<a href="{$url_prefix}/real-estate/" class="btn m-light-magenta">{$lang->get('Искать в строящихся домах', 'Search in new objects')}</a>
		</div>
		{if !empty($site_config.resale_consultant)}
			<div class="consultant row a-justify">
				<div class="info w2">
					<div class="title">{$lang->get('<span>Сколько стоит</span>эта квартира?', '<span>What\'s the price</span>of the apartment?')|html}</div>
					<div class="small-descr">{$lang->get('Узнайте по телефону', 'Сall us to find out')}</div>
					{if !empty($contacts.phone)}<div class="phone">{$contacts.phone}</div>{/if}
					<a href="" class="btn m-magenta-fill">{$lang->get('Оставить заявку', 'Send your request')}</a>
					<div class="slash"></div>
				</div>
				{foreach from=$site_config.resale_consultant item=consultant name=cons}
					<div class="person w1">
						{if !empty($consultant.photo)}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
						{if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
						{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
						{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
					</div>
				{/foreach}
			</div>
		{/if}

		{if !empty($best_offers)}
			<div class="swipe-wrap m-see-more">
				<h2 class="main m-vw" title="{$lang->get('Лучшие предложения', 'Best offers')}">{$lang->get('Лучшие предложения', 'Best offers')}</h2>
				<div class="title">{$lang->get('Стоит присмотреться', 'It is worth a closer look')} </div>
				<div class="swiper-container">
					<div class="w4 swiper-wrapper">
						{foreach from=$best_offers item=sim name=sim_n}
							{?$cover = !empty($sim.gallery) ? $sim.gallery->getCover() : null}
							{?$delim = ldelim . "!" . rdelim}
							{?$sim_title = $sim.title|replace:$delim:' '}
							{?$url = !empty($sim->getUrl()) ? $sim->getUrl() : null}
							<div class="swiper-slide m-vw flat-wrap{if $smarty.foreach.sim_n.total == 1} m-center{elseif !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} m-margin{/if}">
								<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url('/img/veil.png'), url({!empty($cover) ? $cover->getUrl(940, 650) : ''}){if !empty($gravity)} {$gravity[$cover.gravity]}{/if}; background-size:cover;"></a>
								<div class='params'>
									<div class="main m-vw">{$lang->get('Жилой комплекс', 'Residential Complex')}</div>
									{if !empty($sim_title)}<div class="title"><span>{$sim_title}</span></div>{/if}
									{if !empty($sim.district.prepositional)}<div class="descr">{$sim.district.prepositional}</div>{/if}
									{if !empty($sim.properties.price_meter_from.value)}
										<div class="area">{$sim.properties.price_meter_from.value} <i>+</i><br><span>{$lang->get('тыс.рублей за м', 'ths rub. per m')}<sup>2</sup></span></div>
									{/if}
									{if !empty($url)}<a href="{$url}" class="btn m-sand m-vw">{$lang->get('Выбрать', 'Choose')}</a>{/if}
								</div>
							</div>
						{/foreach}
					</div>
				</div>
				<div class="swiper-button-prev{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
				<div class="swiper-button-next{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
			</div>
		{/if}
		</div>
		</div>
	{/if}

{elseif $smarty.get.view == 'map'}

	<div class="map-result">
		<div class="map"></div>
		<div class="items-list a-hidden">
			{if !empty($items)}
				{foreach from=$items item=item name=name_n}
					{?$delim = ldelim . "!" . rdelim}
					{?$title = $item.title|replace:$delim:' '}
					{?$cover = !empty($item.gallery) ? $item.gallery->getCover() : null}
					{?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
					<div class="item item-{$item.id}" data-id="{$item.id}" data-coords="{$item.address_coords}">
						<div class="flat-wrap">
							{if !empty($item.icon)}
								<div class="top">
									{if is_array($item.icon)}
                                        {foreach from=$item.icon item=icon}
                                            <div class="skew m-sand-skew">{$icon}</div>
                                            {break}
                                        {/foreach}
                                    {else}
                                        <div class="skew m-sand-skew">{$item.icon}</div>
                                    {/if}
								</div>
							{/if}
							{if !empty($cover)}
								<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url({$cover->getUrl(367, 304)}){if !empty($gravity)}{if !empty($gravity)} {$gravity[$cover.gravity]}{/if}{/if}; background-size:cover;">
								</a>
							{/if}
							<div class="params">
								<div class="main">{$item.address}</div>
								<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>
                                {?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
                                {if !empty($item.wc_number)}{?$wc_number = $lang->get($item.wc_number|plural_form:'санузел':'санузла':'санузлов', $item.wc_number|plural_form:'bathroom':'bathrooms':'bathrooms')}{/if}
                                <div class="descr">
									{if !empty($floors)}{$floors[0]} {$lang->get('уровня', 'floors')}{/if}{if !empty($wc_number) && !empty($floors)}<span> • </span>{/if}
                                    {!empty($wc_number) ? $wc_number : ''}
                                    {if !empty($wc_number) && !empty($item.floor)}<span> • </span>{/if}{if !empty($item.floor)}{$lang->get('Этаж', 'Floor')} {$item.floor} {if $item.number_storeys}{$lang->get('из', 'of')} {$item.number_storeys}{/if}{/if}
								</div>
								{if !empty($item.properties.area_all.value)}
									<div class="area"><i>~</i>{$item.properties.area_all.value|round} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
								{/if}
								{if !empty($item.overhang)}
									<div class="small-descr">
                                    {$lang->get('Есть','With')}&nbsp;
                                    {if is_array($item.overhang)}
                                        {foreach from=$item.overhang item=overhang name=overhang_n}
                                            {$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
                                        {/foreach}
                                    {else}
                                        {$item.overhang}
                                    {/if}
									</div>
								{/if}
								{if !empty($url)}
									<div class="bottom"><a href="{$url}" class="btn m-sand">{$lang->get('В деталях', 'In detail')}</a></div>
								{/if}
							</div>
							<div class="item-offers">
								<div class="main">{$lang->get('Цена', 'Price')}</div>
								<div class="column">
									<div class="col-inner">
										{if !empty($item.price)}
											<div class="price">{$item.properties.price.value} {$lang->get('млн руб.', 'mln rub.')}</div>
											{if !empty($item.area_all)}
												<div class="small-descr">{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м', 'ths rub. per m')}²</div>
											{/if}
										{else}
											<div class="price m-noprice"><div>{$lang->get('по запросу', 'on request')}</div></div>
											<a href="{$url_prefix}/resale/request/?id={$complex.id}" class="small-descr">{$lang->get('Узнать цену','Find out price')}</a>
										{/if}
									</div>
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
	</div>
{/if}
