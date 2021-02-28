{if empty($path)}
	{if !empty($document_root)}
		{?$path = $document_root . "/templates/project/img/svg/"}
	{else}
		{?$path = $smarty.server.DOCUMENT_ROOT . "/templates/project/img/svg/"}
	{/if}
{/if}
{if empty($smarty.get.view) || $smarty.get.view != 'map'}
	{if !empty($items)}
		<div class="list-result">
			<div class="items-list">

					{foreach from=$items item=item name=name_n}

						{?$delim = ldelim . "!" . rdelim}
						{?$title = $item.title|replace:$delim:' '}
						{?$gallery = !empty($item.gallery) ? $item.gallery->getImages() : null}
						{?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
						<div class="item-wrap">
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
													<a href="{if !empty($url)}{$url}{/if}"
													   {if iteration > 1} data-bg-img='{$img->getUrl(372,270)}'{/if} 
														class='cover{if iteration > 1} delay-img{/if}' 
														style="{if iteration < 2}background-image: url({$img->getUrl(372,270)});{/if}{if !empty($gravity)}background-position:{$gravity[$img.gravity]}{/if} background-size:cover;">
													</a>
												{/if}
											</div>
										{/foreach}
									</div>
								</div>

								<div class="cover-controls{if $smarty.foreach.img_n.total < 2} a-hidden{/if}">
                                    {if !empty($item.logo)}<div class="complex-logo" style=""><img src="{$item.logo->getUrl()}" alt=""></div>{/if}
                                    <div class="prev swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
									<div class="next swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
								</div>
							</div>
							<div class="item-offers">
								<div class="main">{$lang->get('квартиры в продаже', 'apartments for sale')}</div>
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
												<a href="{$url_prefix}/real-estate/request/?id={$item.id}" class="a-inline-cont m-link">
													<span class="num">{iteration}{iteration == 5 ? ' +' : ''}</span>
													<span class="slash"></span>
													<span class="empty">{$lang->get('По запросу', 'on request')}</span>
												</a>
											{/if}
										{/section}
									</div>
								</div>
							</div>
							<a href="{if !empty($url)}{$url}{/if}" class="item-params">
								<div class="main">{$lang->get('Жилой комплекс', 'Residential Complex')} </div>
								<div class="title"><span>{$title}</span></div>
								<div class="descr">{if !empty($item.district.prepositional)}{$item.district.prepositional}{/if}</div>
								{if !empty($item.properties.price_meter_from.value)}
									<div class="area">{$item.properties.price_meter_from.value}<i>+</i></div>
									<div class="small-descr">{$lang->get('тыс.рублей за м²', 'ths rub. per m²')}</div>
								{/if}
								{if !empty($url)}
									<div class="bottom"><div class="btn m-sand">{$lang->get('В деталях', 'IN DETAIL')}</div></div>
								{/if}
							</a>
						</div>
					{/foreach}

			</div>
            
            {if empty($smarty.get.page)} 
                {? $page_num = 1 }
            {else} 
                {? $page_num = $smarty.get.page }
            {/if}
            
            {if !($pageSize > count($items))}
                {?$cur_count_items = $page_num * count($items)}
            {else}
                {?$cur_count_items = count($items)}
            {/if}
				
			{?$items_rest = $count - $cur_count_items}
			{if $items_rest > $cur_count_items}{?$items_rest = $cur_count_items}{/if}
            
			{if !empty($count) && $count > count($items) && !($cur_count_items < $pageSize)}
				<div class="more-row a-center">
					<div class="see-more"{if empty($smarty.get.page)} data-page="2"{else} data-page="{$smarty.get.page+1}"{/if}>
						{if $request_segment.key == 'ru'}
							Показать еще {$items_rest|plural_form:'объект':'объекта':'объектов'}
						{else}
							Show {$items_rest} more offers
						{/if}
					</div>
				</div>
			{/if}
		</div>
	{else}
		<div class="list-result m-empty">
			<div class="items-list">
				<div class="resale m-gray">
					<div class="main">{$lang->get('К нашему великому сожалению', 'Unfortunately')}</div>
					<div class="title"><span>{$lang->get('Вариантов с заданными параметрами не найдено', "We couldn't find appropriate offers with parameters you've set")}</span></div>
					<div class="descr">{$lang->get('А почему бы не изучить похожие предложения в готовых домах?', 'Why not check some apartments fo resale?')}</div>
					<a href="{$url_prefix}/resale/" class="btn m-light-magenta">{$lang->get('Искать на вторичном рынке', 'Search for resale apartments')}</a>
				</div>
				{if !empty($site_config.real_estate_consultant)}
					<div class="consultant row a-justify">
						<div class="info w2">
							<div class="title"><span>{$lang->get('Сколько стоит', 'What\'s the price')}</span>{$lang->get('эта квартира?', 'of the apartment?')}</div>
							<div class="small-descr">{$lang->get('Узнайте по телефону', 'Сall us to find out')}</div>
							{if !empty($contacts.phone)}<div class="phone">{$contacts.phone}</div>{/if}
							<a href="" class="btn m-magenta-fill">{$lang->get('Оставить заявку', 'Send your request')}</a>
							<div class="slash"></div>
						</div>
						{foreach from=$site_config.real_estate_consultant item=consultant name=cons}
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
									{foreach from=$item.icon item=icon}
										<div class="skew m-sand-skew">{$icon}</div>
										{break}
									{/foreach}
								</div>
							{/if}
							{if !empty($cover)}
								<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url({$cover->getUrl(367, 304)}){if !empty($gravity)}{if !empty($gravity)} {$gravity[$cover.gravity]}{/if}{/if}; background-size:cover;">
								</a>
							{/if}
							<div class='params'>
								<div class="main">{$lang->get('Жилой комплекс', 'Residential Complex')}</div>
								<div class="title"><span>{$title}</span></div>
								<div class="descr">{if !empty($item.district.prepositional)}{$item.district.prepositional}{/if}</div>
								{if !empty($item.properties.price_meter_from.value)}
									<div class="area">{$item.properties.price_meter_from.value}<i>+</i></div>
									<div class="small-descr">{$lang->get('тыс.рублей за м²', 'ths rub. per m²')}</div>
								{/if}
								{if !empty($url)}
									<div class="bottom"><a href="{$url}" class="btn m-sand">{$lang->get('В деталях', 'In detail')}</a></div>
								{/if}
							</div>
							<div class="item-offers">
								<div class="main">{$lang->get('Квартиры в продаже', 'Apartments for sale')}</div>
								<div class="column">
									{section loop=5 name=bedroom_count}
										{if !empty($bedroom_count_filters[$item.id][iteration])}
											{?$bedroom = $bedroom_count_filters[$item.id][iteration]}
											<a href="{$item->getUrl()}apartments/?bed_number[]={$bedroom['bedroom_count']}" class="">{$bedroom['bedroom_count']}{$bedroom['bedroom_count'] == 5 ? '+' : ''}</a>
										{else}
											<div>{iteration}{iteration == 5 ? ' +' : ''}</div>
										{/if}
									{/section}
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
		</div>
	</div>
{/if}