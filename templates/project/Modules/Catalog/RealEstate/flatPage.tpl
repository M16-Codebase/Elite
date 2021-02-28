{?$floor = $item->getParent()}
{?$corpus = $floor->getParent()}
{?$complex = $corpus->getParent()}

{? $no_index_follow = true}


{if !empty($item.shemes)}{?$gallery = $item.shemes->getImages()}{/if}
{?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
{?$cover = !empty($item.shemes) ? $item.shemes->getCover() : null}

{?$delim = ldelim . "!" . rdelim}
{?$title_arr = $delim|explode:$complex.title}
{?$title = $complex.title|replace:$delim:' '}
{if !empty($item.bed_number)}{?$bed_number = $lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}{?$bed_number = (' '|explode:$bed_number)}{/if}
{?$alt_before = $title}
{if !empty($corpus.title) }{?$alt_before .= ', ' . $lang->get('Корпус', 'Building') . ' ' . $corpus.title}{/if}
{if (!empty($bed_number[0]) && !empty($bed_number[1])) }
	{?$alt_before .= ', ' . $bed_number[0]}
    {?$alt_before .= ' ' .$bed_number[1]}
{/if}
{if !empty($item.area_all) }{?$alt_before .= ', ' . $item.area_all}{/if}

{?$complex_title = $complex.title}
{?$delim = ldelim . "!" . rdelim}
{?$complex_title = $complex_title|replace:$delim:' '}

{?$corpus_desc = ''}
{if !empty($corpus.title)}{? $corpus_desc = ', ' . $lang->get('корпус', 'building') . '-' . $corpus.title}{/if}
{?$corp = !empty($corpus_desc) ? $corpus_desc . ', ' : ''}
{if $request_segment.key == 'ru'}
    {?$alt = $alt_before . ' | планировка элитных квартир Санкт-Петербурга | М16'}

    {?$pageTitle = 'Купить ' . $item.bed_number . '-комнатную квартиру в ' . $complex_title
		. $corpus_desc . ', ' . $item.area_all . ', ' . $floor.title . ' этаж в СПб'}

	{?$pageDescription = 'Квартиры в элитных новостройках в ' . $complex.district.prepositional . ' Санкт-Петербурга. '
		. $item.bed_number . '-комнатная квартира в ' . $complex_title|strip_tags . ' (СПб), ' . $corp . $item.area_all . ', '
		. $floor.title . ' этаж. Квартира по цене от застройщика.'}

	{*}{if $item.bed_number == 1}
		{?$pageTitle = "Продажа элитных однокомнатных квартир в СПб | Агентство «М16-Недвижимость»"}
		{?$pageDescription = "Элитные 1-комнатные квартиры в разных районах Санкт-Петербурга. Квартиры в новостройках. Продажа от агентства недвижимости Вячеслава Малафеева «М16-Недвижимость». Высококлассный сервис, широкий спектр услуг, спортивные подарки всем клиентам."}

    {elseif $item.bed_number == 2}
		{?$pageTitle = "Элитные двухкомнатные квартиры в новостройках СПб | Агентство «М16-Недвижимость»"}
        {?$pageDescription = "На этой странице представлен каталог элитных 2-комнатных квартир Санкт-Петербурга. Лучшие предложения на первичном рынке, разные районы города, широкий спектр площадей и разнообразные планировочные решения. "}
    {elseif $item.bed_number == 3}
        {?$pageTitle = "Купить 3-комнатную квартиру в СПб | Элитные квартиры в агентстве «М16-Недвижимость»"}
        {?$pageDescription = "Продажа трехкомнатных квартир в разных районах СПб. У нас вы можете купить квартиру в лучших новостройках города! Квартиры классического и европейского формата, широкий выбор планировочных решений и метража."}
    {elseif $item.bed_number == 4}
        {?$pageTitle = "Купить 4х-комнатную квартиру в СПб | Агентство элитной недвижимости «М16»"}
        {?$pageDescription = "Четырехкомнатные квартиры в новостройках СПб: цены, широкий выбор предложений, разнообразные районы! Лучшие предложения на рынке элитной недвижимости! Каталог квартир повышенной комфортности. "}
    {elseif $item.bed_number == 5}
        {?$pageTitle = "5-комнатные квартиры в элитных новостройках СПб | Продажа в агентства «М16-Недвижимость» "}
        {?$pageDescription = "Каталог пяти- и шестикомнатных квартир в элитных новостройках Санкт-Петербурга. Цены, фото, подробные планировки и описание. Широкий выбор локаций, планировоки метража. Купите 5-комнатную квартиру по цене от застройщика!"}
	{/if}*}
{else}
    {?$alt = $alt_before . ' | planning of elite apartments in St. Petersburg | М16'}

    {?$pageTitle = 'Buy ' . $item.bed_number . '- bedroom apartment in ' . $complex_title|strip_tags
		. $corpus_desc . ', ' . $item.area_all . ', ' . $floor.title . ' floor in St. Petersburg'}

    {?$pageDescription = 'Apartments in the elite new buildings in the ' . $complex.district.prepositional . ' of St. Petersburg. '
		. $item.bed_number . '-bedroom apartment in ' . $complex_title|strip_tags . ' (SPb), ' . $corp . $item.area_all . ', '
		. $floor.title . ' floor. Apartment for the price of the builder.'}
{*}
    {if $item.bed_number == 1}
        {?$pageTitle = "Luxury one-bedroom apartments for sale in St. Petersburg | Agency \"M16-Real Estate\""}
        {?$pageDescription = "Elite 1-room apartments in different districts of St. Petersburg. Apartments in new buildings. Sale from real estate agency Vyacheslav Malafeev \"M16-Real Estate\". High-class service, a wide range of services, sports gifts to all customers."}

    {elseif $item.bed_number == 2}
        {?$pageTitle = "Luxury one-bedroom apartments in new buildings in St. Petersburg | Agency \"M16-Real Estate\""}
        {?$pageDescription = "On this page you can find a catalog of elite 2-room apartments in St. Petersburg. The best offers in the primary market, different parts of the city, a wide range of areas and a variety of planning solutions."}
    {elseif $item.bed_number == 3}
        {?$pageTitle = "Buy a 3-bedroom apartment in St. Petersburg | Elite apartments in the agency \"M16-Real Estate\""}
        {?$pageDescription = "Sale of three-room apartments in different areas of St. Petersburg. Here you can buy an apartment in the best new buildings of the city! Apartments of classical and European format, a wide choice of planning solutions and footage."}
    {elseif $item.bed_number == 4}
        {?$pageTitle = "Buy a 4-room apartment in St. Petersburg | The elite real estate agency \"M16\""}
        {?$pageDescription = "Four-room apartments in new buildings of St. Petersburg: prices, a wide range of offers, various areas! The best offers in the elite real estate market! Catalog of luxury apartments."}
    {elseif $item.bed_number == 5}
        {?$pageTitle = "5-room apartments in elite new buildings of St. Petersburg | Sale in the agency \"M16-Real Estate\""}
        {?$pageDescription = "Catalog of five- and six-room apartments in elite new buildings of St. Petersburg. Prices, photos, detailed layouts and description. A wide range of locations, space planning. Buy a 5-room apartment at a price from the builder!"}
    {/if}*}
{/if}
{if !empty($complex.gallery)}{?$complex_cover = $complex.gallery->getCover()}{/if}



{?$og_meta = array()}
{?$og_meta['type'] = 'website'}
{?$og_meta['site_name'] = 'М16-Недвижимость'}
{?$og_meta['title'] = $pageTitle}
{?$og_meta['description'] = $pageDescription}
{?$og_meta['url'] = $page_url}
{?$og_meta['locale'] = 'ru_RU'}

{if !empty($complex_cover)}
    {?$og_meta['image'] = $complex_cover->getCleanUrl()}
    {?$og_meta['width'] = $complex_cover->getWidth()}
    {?$og_meta['height'] = $complex_cover->getHeight()}
{/if}


{include file='/components/main_menu.tpl'}
<div class="top-bg" id="site-top">
	{if !empty($complex->getUrl())}
		<a href="{$complex->getUrl()}apartments/" class="back">{fetch file=$path . "arrow.svg"}</a>
	{/if}
	<div class="main">{$lang->get('Квартира в жилом комплексе', 'Apartment in residential complex')}</div>
	<div class='bg-img' style='background: url(/img/veil.png), url({!empty($complex_cover) ? $complex_cover->getUrl() : ''}); background-size:cover;'></div>

    <div class="breadcrumbs">
		<ul>
			<li><a href="/">Главная</a></li>
			<span class="slash"></span>
			<li><a href="/real-estate/">Строящаяся недвижимость</a></li>
			<span class="slash"></span>
			<li><a href="{$complex->getUrl()}">{$title}</a></li>
			<span class="slash"></span>
			<li><span>{if !empty($corpus.title)}{$lang->get('Корпус', 'Building')} {$corpus.title}{/if}{if !empty($bed_number[0]) && !empty($bed_number[1]) && $corpus.title} <i>•</i> {/if}{if !empty($bed_number[0]) && !empty($bed_number[1])}{$bed_number[0]} {$bed_number[1]}{/if}{if !empty($item.area_all) && !empty($bed_number[0]) && !empty($bed_number[1])} <i>•</i> {$item.area_all}{/if}</span></li>
		</ul>
    </div>
    <div class="site-top">
		{if !empty($title)}
			<h1 class="title" title="{$title} {!empty($corpus.title) ? $corpus.title : ''}{if !empty($bed_number[0]) && !empty($bed_number[1])} {$bed_number[0]} {$bed_number[1]}{/if}{if !empty($item.area_all)} {$item.area_all}{/if}">
				<span>{$title}</span><br>{if !empty($corpus.title)}{$lang->get('Корпус', 'Building')} {$corpus.title}{/if}{if !empty($bed_number[0]) && !empty($bed_number[1]) && $corpus.title} <i>•</i> {/if}{if !empty($bed_number[0]) && !empty($bed_number[1])}{$bed_number[0]} {$bed_number[1]}{/if}{if !empty($item.area_all) && !empty($bed_number[0]) && !empty($bed_number[1])} <i>•</i> {$item.area_all}{/if}
			</h1>
		{/if}
	</div>
</div>

<div class="page-center m-main">
	{if !empty($item.special_offer) && !empty($item.special_offer.comment)}
		<div class="special">
			<div class="skew m-sand-skew">{$lang->get('Акция', 'Promo')}</div>
			<div class="special-text"><div>{$item.special_offer.comment}</div></div>
		</div>
	{/if}
	<div class="item-wrap gallery-tiles">
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


		{if !empty($gallery)}
			<div class="gallery-big gallery-main">
				<div class="close-photo" title="{$lang->get('Закрыть изображение', 'Close image')}">{fetch file=$path . "close.svg"}</div>
				<div class="swiper-container">
					<div class="swiper-wrapper">
						{foreach from=$gallery item=image name=gallery_swiper}
							<div class="swiper-slide img-{$image.id} delay-img"
								data-bg-img='{$image->getUrl(1900,950)}'
								style="background-position:{if !empty($gravity)} {$gravity[$image.gravity]}{/if};background-repeat:no-repeat;background-size:contain;">
							</div>
						{/foreach}
					</div>
					<div class="swiper-button-prev"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
					<div class="swiper-button-next"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
				</div>
			</div>
		{/if}
		<div class="gallery-small">
			<div class="flat-wrap m-vw">
				{if !empty($gallery)}
					<div class="img-wrap">
						<div class="swiper-container">
							<div class="w4 swiper-wrapper">
								{foreach from=$gallery item=img name=img_n}
									<div class="swiper-slide open-photo" data-id="{$img.id}">
										<img src="{$img->getUrl(850,570)}){if !empty($gravity)} {$gravity[$img.gravity]}{/if}"
											 alt="{$alt}"
											 title="{$alt}">
										<!--<div class="cover" style="background:url({$img->getUrl(850,570)}){if !empty($gravity)} {$gravity[$img.gravity]}{/if}; background-size:cover;"></div>-->
									</div>
								{/foreach}
							</div>
						</div>
						{if !empty($smarty.foreach.img_n.total) && $smarty.foreach.img_n.total > 1}
						<div class="nav">
							<div class="pagin">1 / 1</div>
							<div class="swiper-pagination"></div>
							<div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
							<div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
						</div>
						{/if}
					</div>
				{else}

					<div class="img-wrap">
											<div class="swiper-container">
												<div class="w4 swiper-wrapper">
													<div class="swiper-slide open-photo">
														<div class="cover" style="background:url('/img/not_plane.jpg'); background-size:cover;"></div>
													</div>
												</div>
											</div>
										</div>
				{/if}

				<div class='params'>
					<div class="main">{$lang->get('Квартира', 'Apartment')}</div>
					{if !empty($item.bed_number)}<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>{/if}
					{if !empty($item.properties.area_all.value)}<div class="area">{$item.properties.area_all.value} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>{/if}
					{if !empty($item.overhang)}
						<div class="small-descr">
							{$lang->get('Есть ', 'With ')}
							{foreach from=$item.overhang item=overhang name=overhang_n}
								{$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
							{/foreach}
						</div>
					{/if}
					{if !empty($item.finishing)}
						<div class="small-descr">
							{$item.finishing}
						</div>
					{/if}
					<a href="{$item->getUrl($request_segment.id)}?pdf" class="btn m-sand m-vw js-pdf">{$lang->get('Сохранить в pdf', 'Save to PDF')}</a>
					<div class="price">
						<div class="full_price">{if !empty($item.price)}{$item.properties.price.value} {$lang->get('млн руб.', 'mln rub.')}{else}{$lang->get('Узнайте цену', 'Find out the price')}{/if}</div>
						<div class="price_per_m">{if !empty($item.properties.area_all.value) && !empty($item.price)}{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м<sup>2</sup>', 'ths rub. per m<sup>2</sup>')|html}{else} {$lang->get('По запросу', 'By request')}{/if}</div>
					</div>
					<a href="{$url_prefix}/real-estate/request/?id={$item.id}" class="btn m m-magenta-fill m-vw">{$lang->get('Оставить заявку', 'Send your request')}</a>
				</div>
				<div class="favorite{if $item.in_favorites} m-added{/if}" {if !empty($item.id) && !empty($moduleUrl)}data-id="{$item.id}" data-url="{$moduleUrl}"{/if}>{fetch file=$path . "favorite.svg"}</div>
			</div>
			<div class="floor-wrap">
				{?$parent_item = $item->getParent()}
				{if !empty($parent_item.properties.sheme_get.complete_value)}
					<div class="floor-block">
						<div class="main">этаж</div>
						<div class="title"><span>{$parent_item.floor_number} {$lang->get('этаж', 'floor')}</span></div>
						<div class="img-cont">
							{if !empty($item.sheme_coords)}
								{if $item.properties.sheme_coords.set == 1}
									{?$poly_coords = implode('|', $item.sheme_coords)}
								{else}
									{?$poly_coords = $item.sheme_coords}
								{/if}
							{else}
								{?$poly_coords = ''}
							{/if}
							<img src="{$parent_item.properties.sheme_get.complete_value->getUrl(500, 200, false, true, array('gray'))}" data-coords="{$poly_coords}" class="poly-scheme" alt="" />
						</div>
					</div>
				{/if}
				{?$grandparent_item = $parent_item->getParent()}
				{if !empty($grandparent_item.properties.sheme_get.complete_value)}
					<div class="floor-block">
						<div class="main">{$lang->get('Корпус', 'Building')}</div>
						<div class="title"><span>{$lang->get('Корпус', 'Building')} {$grandparent_item.title}</span></div>
						<div class="img-cont">
							{if !empty($parent_item.sheme_coords)}
								{if $parent_item.properties.sheme_coords.set == 1}
									{?$poly_coords = implode('|', $parent_item.sheme_coords)}
								{else}
									{?$poly_coords = $parent_item.sheme_coords}
								{/if}
							{else}
								{?$poly_coords = ''}
							{/if}
							<img src="{$grandparent_item.properties.sheme_get.complete_value->getUrl(500, 200, false, true, array('gray'))}" data-coords="{$poly_coords}" class="poly-scheme" alt="" />
						</div>
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>
	{if !empty($item.features)}
		<div class="item-benefits row a-justify">
			<div class="ben-title main m-vw w2">
				{$lang->get('Достоинства квартиры', 'Advantages of the apartment')}
			</div>
			<div class="ben-list w2">
				{foreach from=$item.features item=benefit}
					<div class="ben-item">{$benefit}</div>
				{/foreach}
			</div>
		</div>
	{/if}

{?$options_small = array()}
{?$options_big = array()}
{if !empty($item.properties.area_all.value)}
	{capture assign=option_area_all}
		<div class="w1">
			<div class="big-opt">{$item.properties.area_all.value} <span class="small-opt"> {$lang->get('м', 'm')}<sup>2</sup></span></div>
			<div class="opt-descr">{$lang->get('Общая площадь', 'Total area')}</div>
		</div>
	{/capture}
	{?$options_small[] = $option_area_all}
{/if}
{if !empty($item.properties.area_kitchen.value)}
	{capture assign=option_area_kitchen}
		<div class="w1">
			<div class="big-opt">{$item.properties.area_kitchen.value} <span class="small-opt"> {$lang->get('м', 'm')}<sup>2</sup></span></div>
			<div class="opt-descr">{$lang->get('Площадь кухни', 'Kitchen area')}</div>
		</div>
	{/capture}
	{?$options_small[] = $option_area_kitchen}
{/if}
{if !empty($item.properties.area_living.value)}
	{capture assign=option_area_living}
		<div class="w2">
			<div class="big-opt">{$item.properties.area_living.value} <span class="small-opt"> {$lang->get('м', 'm')}<sup>2</sup></span></div>
			<div class="opt-descr">{$lang->get('Жилая площадь', 'Living area')}</div>
		</div>
	{/capture}
	{?$options_big[] = $option_area_living}
{/if}
{if !empty($item.properties.ceiling_height.value)}
	{capture assign=option_ceiling_height}
		<div class="w1">
			<div class="big-opt">{$item.properties.ceiling_height.value}</div>
			<div class="opt-descr">{$lang->get('Высота потолков', 'Ceiling height')}</div>
		</div>
	{/capture}
	{?$options_small[] = $option_ceiling_height}
{/if}
{if !empty($complex.payment_types)}
	{capture assign=option_payment_types}
		<div class="w1 m-commis">
			<div class="big-opt m-sand"><div class="opt-descr">{$lang->get('Комисссия<br>при покупке', 'Agency<br>Comission')|html}</div>0%</div>
			<div class="opt-descr m-sand">
				{foreach from=$complex.payment_types item=payment_types name=payment}
					{$payment_types}{if !$smarty.foreach.payment.last} <span>•</span>{/if}
				{/foreach}
			</div>
		</div>
	{/capture}
	{?$options_small[] = $option_payment_types}
{/if}
{?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
{if !empty($item.wc_number)}{?$wc_number = $lang->get($item.wc_number|plural_form:'санузел':'санузла':'санузлов', $item.wc_number|plural_form:'bathroom':'bathrooms':'bathrooms')}{?$wc_number = (' '|explode:$wc_number)}{/if}
{capture assign=option_floors}
	<div class="w2 m-space">
		<div class="big-opt">
			{if !empty($floors[0])}{$floors[0]}{/if}
			{if !empty($bed_number[0])}{if !empty($floors[0])}/ {/if}{$bed_number[0]}{/if}
			{if !empty($wc_number[0])}{if !empty($floors[0]) || !empty($bed_number[0])}/ {/if}{$wc_number[0]}{/if}
		</div>
		<div class="opt-descr">
			{if !empty($floors[1])}{$floors[1]}{/if}{if !empty($bed_number[1])} {$bed_number[1]}{/if}{if !empty($wc_number[1])} {$wc_number[1]}{/if}
		</div>
	</div>
{/capture}
{?$options_big[] = $option_floors}

{if count($options_small) || count($options_big)}
	<div class="options" id="contacts-map">
		<div class="slash m-left"></div>
		<div class="slash m-right"></div>
		<div class="row">
			{if count($options_small)}{array_shift($options_small)|html}{else}<div class="w1"></div>{/if}
			{if count($options_small)}{array_shift($options_small)|html}{else}<div class="w1"></div>{/if}
			{if count($options_big)}{array_shift($options_big)|html}{else}<div class="w2"></div>{/if}
		</div>
		{if count($options_small) || count($options_big)}
			<div class="row">
				{if count($options_small)}{array_shift($options_small)|html}{else}<div class="w1"></div>{/if}
				{if count($options_small)}{array_shift($options_small)|html}{else}<div class="w1"></div>{/if}
				{if count($options_big)}{array_shift($options_big)|html}{else}<div class="w2"></div>{/if}
			</div>
		{/if}
	</div>
{/if}

{if !empty($complex.consultant)}
	{?$consultants = $complex.consultant}
{elseif !empty($site_config.real_estate_consultant)}
	{?$consultants = $site_config.real_estate_consultant}
{else}
	{?$consultants = null}
{/if}
{if !empty($consultants)}
	<div class="consultant row a-justify">
		<div class="info w2">
			<div class="title">{if !empty($item.price)}{$lang->get('<span>Заинтересовала</span>эта квартира?', '<span>This apartment</span>looks interesting?')|html}{else}{$lang->get('<span>Сколько стоит</span>эта квартира?', '<span>What\'s the price</span>of the apartment?')|html}{/if}</div>
			<div class="small-descr">{$lang->get('Узнайте больше по телефону', 'Know more by calling')}</div>
			{if !empty($contacts.phone)}<div class="phone">{$contacts.phone}</div>{/if}
			<a href="{$url_prefix}/real-estate/request/?id={$item.id}" class="btn m-magenta-fill">{$lang->get('Оставить заявку', 'Send your request')}</a>
			<div class="slash"></div>
		</div>
		{foreach from=$consultants item=consultant name=cons}
			<div class="person w1">
				{if !empty($consultant.photo)}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
				{if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
				{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
				{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
			</div>
		{/foreach}
	</div>
{/if}

{* перенесено со станицы комплекса *}

<div class="contacts-map" id="contacts-map" itemscope itemtype="http://schema.org/Place">
    {if !empty($complex.address_coords)}
		<div class="map-big">
			<div class="close-map" title="{$lang->get('Закрыть карту', 'Close map')}">{fetch file=$path . "close.svg"}</div>
			<div class="map" data-coords="{$complex.address_coords}"></div>
			<div class="infoblock-content a-hidden">
				<div class="map-item-content">
					<div class="item-type main">{$lang->get('Жилой комплекс', 'Residential Complex')}</div>
					<div class="item-title descr-big">{$title}</div>
					<div class="address">{$complex.address}</div>
				</div>
			</div>
			<div class="infra-markers">
                {foreach from=$complex.infra item=infra}
                    {if !empty($infra.address_coords)}
						<div class="marker" data-coords="{$infra.address_coords}" data-title="{if !empty($infra.title)}{$infra.title}{/if}"{if !empty($infra.type)} data-img="/img/infra/{$infra.properties.type.value_key}.png{/if}"></div>
                    {/if}
                {/foreach}
			</div>
		</div>
    {/if}
	<div class="map-small">
		<div class="page-center">
			<h2 class="main-top main" title="{$lang->get('Расположение и окружение объекта', 'OBJECT LOCATION AND SURROUNDINGS')}">{$lang->get('Расположение и окружение объекта', 'OBJECT LOCATION AND SURROUNDINGS')}</h2>
			<div class="titles">
                {if !empty($complex.district.title)}<div class="distr title"><span>{$complex.district.title}</span></div>{/if}
                {if !empty($complex.address)}<div class="address descr-big m-vw"><span itemprop="streetAddress">{$complex.address}</span></div>{/if}
			</div>
            {if !empty($complex.address_coords)}
				<div class="open-map">
					<div class="btn m-magenta-fill">
						<span class="open-map-cover"></span>
                        {fetch file=$path . "expand.svg"} {$lang->get('Открыть большую карту', 'Open Large Map')}
					</div>
					<div class="marker"></div>
					<div class="map-lock"></div>
					<div class="map-wrap">
						<div itemprop="hasMap" class="map" data-coords="{$complex.address_coords}"></div>
					</div>
				</div>
            {/if}
			<div class="map-info row">
				<div class="w1 a-left">
                    {if !empty($complex.metro) && count($complex.metro)}
						<div class="info-block">
							<div class="main">{$lang->get('Станция метро', 'Subway Station')}</div>
                            {foreach from=$complex.metro item=metro}
								<div class="val">{$metro.variant_title}</div>
                            {/foreach}
						</div>
                    {/if}
                    {if !empty($complex.center_distance) || !empty($complex.center_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Центр петербурга', 'City Center')}</div>
							<div class="val">{$complex.center_distance} {if !empty($complex.center_distance) && !empty($complex.center_time_bycar)} — {/if}{$complex.center_time_bycar} </div>
						</div>
                    {/if}
				</div>
				<div class="w1 a-right">
                    {if !empty($item.airport_distance) || !empty($item.airport_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Аэропорт пулково', 'Pulkovo Airport')}</div>
							<div class="val">{$complex.airport_distance} {if !empty($complex.airport_distance) && !empty($complex.airport_time_bycar)} — {/if}{$complex.airport_time_bycar} </div>
						</div>
                    {/if}
                    {if !empty($complex.kad_distance) || !empty($complex.kad_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Кольцевая автодорога', 'Ring Road')}</div>
							<div class="val">{$complex.kad_distance} {if !empty($complex.kad_distance) && !empty($complex.kad_time_bycar)} — {/if}{$complex.kad_time_bycar} </div>
						</div>
                    {/if}
				</div>
				<div class="w2">
                    {if !empty($complex.district)}
                        {if !empty($complex.district.post.annotation)}<p class="quote">{$complex.district.post.annotation}</p>{/if}
                        {if !empty($complex.district.post) && $complex.district.post.status == 'close' && $complex.district.status == '3'}
							<a href="{$complex.district->getUrl($request_segment.id)}" class="about-url">— <span>{$lang->get('Узнайте больше об этом районе','Find out more about the area')}</span></a>
                        {/if}
                    {/if}
				</div>
			</div>
		</div>
	</div>
</div>

{if !empty($url)}
	<div class="qr-block-wrap">
		<div class="qr-block">
			<div class="qr">
				<div class="qr-wrap">
					<img src="https://chart.googleapis.com/chart?cht=qr&chs=235x235&chld=L|2&chl=http://{$smarty.server.SERVER_NAME}{$url}" alt="" />
				</div>
			</div>
			<div class="main">{$lang->get('Откройте страницу на смартфоне', 'Open this page on your smartphone')}</div>
			<div class="small-descr">{$lang->get('Нажмите, чтобы увеличить QR-код', 'Press it to enlarge QR code')}</div>
		</div>
	</div>
{/if}


{? $title_ = $lang->get('Квартира в жилом комплексе', 'Apartment in residential complex')}
{if !empty($title)}
    {? $title_ .= $title}
    {if !empty($corpus.title)}
        {? $title_ .= $corpus.title . ' '}
    {/if}
	{if !empty($bed_number[0]) && !empty($bed_number[1])}
        {? $title_ .= $bed_number[0] . $bed_number[1] . ' '}
	{/if}
	{if !empty($item.area_all) && !empty($bed_number[0]) && !empty($bed_number[1])}
        {? $title_ .= $item.area_all}
	{/if}
{/if}


{literal}

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Place",
  "name": "{/literal}{$title_}{literal}",
  "url": "{/literal}{$page_url}{literal}",
  "address": {
		"@type": "PostalAddress",{/literal}
		{if !empty($complex.address)}
			{literal}"streetAddress": "{/literal}{$complex.address}{literal}",{/literal}
    	{/if}
		{if !empty($complex.district.title)}
			{literal}"addressLocality": "{/literal}{$complex.district.title}{literal}"{/literal}
    	{/if}
		{literal}
	  },{/literal}
	  {if !empty($gallery)}
		{literal}"photo": {
  			"@type": "ImageObject",
  			"url": "{/literal}{$root_url . $img->getCleanUrl()}{literal}"
  			}{/literal}
	  {/if}{literal}
}
</script>
{/literal}
