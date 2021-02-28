{if $request_segment.key == 'ru'}
	{?$alt = $item.title . ' | фото элитной вторичной недвижимости Санкт-Петербурга | М16'}
	{?$apartment_title = 'Снять элитную ' . $item.bed_number . '-комнатную квартиру общей площадью ' . $item.area_all . (!empty($item.district) ? ', ' . $item.district.title : '')}
	{?$pageTitle = $apartment_title .' | М16-Недвижимость'}
	{?$pageDescription = $apartment_title . ' — лучшая элитная недвижимость для самых взыскательных клиентов от агентства недвижимости Вячеслава Малафеева'}



    {if $item.bed_number == 1}
        {?$pageDescription = "Объявление об аренде элитной однокомнатной квартиры ". $item.area_all .". ". $item.district.title .", в г. Санкт-Петербург, ".$item.title.". Стоимость аренды - ".$item.properties.price.value." тыс руб. в месяц. Аренда элитных квартир в Санкт-Петербурге от М16-Недвижимость."}
    {elseif $item.bed_number == 2}
        {?$pageDescription = "Объявление об аренде элитной двухкомнатной квартиры ". $item.area_all .". ". $item.district.title .", в г. Санкт-Петербург, ".$item.title.". Стоимость аренды - ".$item.properties.price.value." тыс руб. в месяц. Аренда элитных квартир в Санкт-Петербурге от М16-Недвижимость."}
    {elseif $item.bed_number == 3}
        {?$pageDescription = "Объявление об аренде элитной трёхкомнатной квартиры ". $item.area_all .". ". $item.district.title .", в г. Санкт-Петербург, ".$item.title.". Стоимость аренды - ".$item.properties.price.value." тыс руб. в месяц. Аренда элитных квартир в Санкт-Петербурге от М16-Недвижимость."}
    {elseif $item.bed_number == 4}
        {?$pageDescription = "Объявление об аренде элитной четырёхкомнатной квартиры ". $item.area_all .". ". $item.district.title .", в г. Санкт-Петербург, ".$item.title.". Стоимость аренды - ".$item.properties.price.value." тыс руб. в месяц. Аренда элитных квартир в Санкт-Петербурге от М16-Недвижимость."}
    {elseif $item.bed_number == 5}
        {?$pageDescription = "Объявление об аренде элитной пятикомнатной квартиры ". $item.area_all .". ". $item.district.title .", в г. Санкт-Петербург, ".$item.title.". Стоимость аренды - ".$item.properties.price.value." тыс руб. в месяц. Аренда элитных квартир в Санкт-Петербурге от М16-Недвижимость."}
	{/if}

{else}
    {?$alt = $item.title . ' | photo of elite secondary real estate in St. Petersburg | М16'}
	{?$apartment_title = 'Elite ' . $item.bed_number . '-room apartment with total area of ' . $item.area_all . (!empty($item.district) ? ', ' . $item.district.title : '')}
	{?$pageTitle = $apartment_title .' | M16 Real Estate Agency'}
	{?$pageDescription = $apartment_title . ' — the best luxury property for the most demanding clients at M16 Real Estate Agency of Vyacheslav Malafeyev'}


{*    {if $item.bed_number == 1}
        {?$pageTitle = " Elite one-room apartments in St. Petersburg, secondary market | Apartments for sale from M16-Real Estate"}
        {?$pageDescription = "Do you want to buy an elite one-bedroom apartment in St. Petersburg? The best offers of the secondary market - only in the agency \"M16-Real Estate\"! Catalog of apartments with prices, photos, planners and detailed descriptions."}
    {elseif $item.bed_number == 2}
        {?$pageTitle = "Do you want to buy an elite one-bedroom apartment in St. Petersburg? The best offers of the secondary market - only in the agency \"M16-Real Estate\"! Catalog of apartments with prices, photos, planners and detailed descriptions."}
        {?$pageDescription = "Luxury one-bedroom apartments for sale in different parts of St. Petersburg. Prices, planning decisions, photos, descriptions - in the cards of objects. Here you can buy a two-room apartment on the secondary market in the segment \"business\" and \"elite\"."}
    {elseif $item.bed_number == 3}
        {?$pageTitle = "Buy a 3-bedroom apartment in St. Petersburg | The elite real estate agency \"M16\""}
        {?$pageDescription = "Sale of three-room apartments in different areas of St. Petersburg. The best offers of the secondary market: a wide choice of layouts, footage, location. The catalog contains prices, photos and descriptions of luxury apartments."}
    {elseif $item.bed_number == 4}
        {?$pageTitle = "4-room luxury apartments in St. Petersburg | Elite real estate from M16"}
        {?$pageDescription = "Do you plan to buy a four-room apartment in St. Petersburg? The catalog of actual offers on the secondary market: prices, photos, various layouts. Sale of 4-room apartments in different parts of the city."}
    {elseif $item.bed_number == 5}
        {?$pageTitle = "Sale of 5-room apartments from the owner | Elite real estate in the agency of Vyacheslav Malafeev"}
        {?$pageDescription = "Do you want to buy a 5-room apartment in St. Petersburg from the owner? Our catalog of five- and six-bedroom apartments includes detailed photos, descriptions, layouts and prices. Apartments in different parts of the city."}
    {/if}*}

{/if}
{if !empty($item.gallery)}{?$cover = $item.gallery->getCover()}{/if}

{?$og_meta = array()}
{?$og_meta['type'] = 'website'}
{?$og_meta['site_name'] = 'М16-Недвижимость'}
{?$og_meta['title'] = $pageTitle}
{?$og_meta['description'] = $pageDescription}
{?$og_meta['url'] = $page_url}
{?$og_meta['locale'] = 'ru_RU'}
{if !empty($cover)}
	{?$og_meta['image'] = $cover->getCleanUrl()}
	{?$og_meta['width'] = $cover->getWidth()}
	{?$og_meta['height'] = $cover->getHeight()}
{/if}

<div class="top-bg" id="site-top">
	<a href="{$url_prefix}/arenda/" class="back">{fetch file=$path . "arrow.svg"}</a>
	<div class='bg-img' style='background: url(/img/veil.png), url({!empty($cover) ? $cover->getUrl() : ''});background-size:cover;'></div>

    <div class="breadcrumbs">
		<ul>
			<li><a href="/">Главная</a></li>
			<span class="slash"></span>
			<li><a href="/arenda/">Аренда недвижимости</a></li>
			<span class="slash"></span>
			<li><span>{$item.title}</span></li>
		</ul>
	</div>

    <div class="site-top">
		<div class="main">{if ($item.typerk=='Квартира')}{$lang->get('Элитная квартира в Санкт-Петербурге', 'Luxury apartment in St.Petersburg')}
		{else}
		{$lang->get('Элитный коттедж в Санкт-Петербурге', 'Luxury cottage in St.Petersburg')}
			{/if}</div>
		<h1 class="title" title="{$item.title}">
			<span>{$item.title}
				<div class="icon-block">
					{if is_array($item.icon)}
						{foreach from=$item.icon item=icon}
							<div class="skew m-sand-skew">{$icon}</div>
						{/foreach}
					{else}
						<div class="skew m-sand-skew">{$item.icon}</div>
					{/if}
				</div>
			</span>
		</h1>
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
		{if !empty($item.gallery)}{?$gallery = $item.gallery->getImages()}{/if}
		{if !empty($gallery)}
			<div class="gallery-big gallery-main">
				<div class="close-photo" title="{$lang->get('Закрыть изображение', 'Close image')}">{fetch file=$path . "close.svg"}</div>
				<div class="swiper-container">
					<div class="swiper-wrapper">
						{foreach from=$gallery item=image name=gallery_swiper}
							<div class="swiper-slide img-{$image.id} delay-img"
								data-bg-img='{$image->getUrl(1900,950)}'
								style="background-position:{if !empty($gravity)} {$gravity[$image.gravity]}{/if};background-size:cover;">
							</div>
						{/foreach}
					</div>
					<div class="swiper-button-prev"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
					<div class="swiper-button-next"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
				</div>
			</div>
		{/if}
		{if !empty($item.shemes)}
			{?$schemes = $item.shemes->getImages()}
			<div class="gallery-big gallery-schemes">
				<div class="close-photo" title="{$lang->get('Закрыть изображение', 'Close image')}">{fetch file=$path . "close.svg"}</div>
				<div class="swiper-container">
					<div class="swiper-wrapper">
						{foreach from=$schemes item=image name=gallery_swiper_scheme}
							<div class="swiper-slide img-{$image.id}" style="background:url({$image->getUrl(1900,950, false, false, array('gray', 'brit|0'))}){if !empty($gravity)} {$gravity[$image.gravity]}{/if} no-repeat; background-size:contain;">
							</div>
						{/foreach}
					</div>
					{if !empty($smarty.foreach.gallery_swiper_scheme.total) && $smarty.foreach.gallery_swiper_scheme.total > 1}
						<div class="swiper-pagination"></div>
						<div class="swiper-button-prev"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
						<div class="swiper-button-next"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
					{/if}
				</div>
			</div>
		{/if}
		<div class="gallery-small">
			<div class="flat-wrap m-vw m-gallery">
				{if !empty($gallery)}
					<div class="img-wrap">
						<div class="swiper-container gallery-thumbs">
							<div class="swiper-wrapper">
								{foreach from=$gallery item=img name=img_n}
									<div class="swiper-slide{if $smarty.foreach.img_n.first} m-current{/if}">
										{if !empty($img->getUrl())}
											<img src="{$img->getUrl(120, 100, true)}" alt="">
										{/if}
									</div>
									{if $smarty.foreach.img_n.iteration == 1 && !empty($item.video)}
										<div class="swiper-slide video-thumb">
											<img src="https://img.youtube.com/vi/{$item.video}/3.jpg" alt="">
										</div>
									{/if}
								{/foreach}
							</div>
						</div>
						<div class="swiper-container gallery-bot">
							<div class="swiper-wrapper">
								{foreach from=$gallery item=img name=img_n}
									{if !empty($img)}
									<div class="swiper-slide open-photo" data-id="{$img.id}">
                                        <!--<img src="{$img->getUrl(850,570)}){if !empty($gravity)} {$gravity[$img.gravity]}{/if}"
											 alt="{$alt}" title="{$alt}">-->
										<div class="cover"
                                            style="background:url({$img->getUrl(850,570)}){if !empty($gravity)} {$gravity[$img.gravity]}{/if};
                                            background-size:cover;"></div>
									</div>
									{/if}
									{if $smarty.foreach.img_n.iteration == 1 && !empty($item.video)}
										<div class="swiper-slide">
											<div class="video-wrap">
												<div class="video-gallery" data-id="{$item.video}"></div>
											</div>
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
				{/if}
				<div class='params'>
					<div class="main">{$item.typerk}</div>
					{if !empty($item.bed_number)}<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>{/if}
					{?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
					{if !empty($item.wc_number)}{?$wc_number = $lang->get($item.wc_number|plural_form:'санузел':'санузла':'санузлов', $item.wc_number|plural_form:'bathroom':'bathrooms':'bathrooms')}{/if}
					<div class="descr">
						{if !empty($floors)}{$floors[0]} {$lang->get('уровня', 'levels')}{/if}{if !empty($wc_number) && !empty($floors)}<span>•</span>{/if}
						{!empty($wc_number) ? $wc_number : ''}
						{if !empty($wc_number) && !empty($item.floor)}<span>•</span>{/if}{if !empty($item.floor)}{$lang->get('Этаж', 'Floor')} {$item.floor} {if $item.number_storeys}{$lang->get('из', 'of')} {$item.number_storeys}{/if}{/if}
					</div>
					{if !empty($item.properties.area_all.value)}<div class="area">{$item.properties.area_all.value} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>{/if}
					{if !empty($item.overhang)}
						<div class="small-descr">
							{$lang->get('Есть ', 'With ')}
                            {if is_array($item.overhang)}
                                {foreach from=$item.overhang item=overhang name=overhang_n}
                                    {$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
                                {/foreach}
                            {else}
                                {$item.overhang}
                            {/if}
						</div>
					{/if}
					<a rel="nofollow" target="_blank" href="{$item->getUrl($request_segment.id)}?pdf" class="btn m-sand m-vw">{$lang->get('Сохранить в pdf', 'Save to PDF')}</a>
					<div class="price">
						<div class="full_price">{if !empty($item.price)}{$item.properties.price.value} {$lang->get('тыс руб.', 'mln rub.')}{else}Узнайте цену{/if}</div>
						<div class="price_per_m">За месяц</div>
					</div>
					<div class="price">
						<div class="full_price">
							{$rent_price}
							{$lang->get(' тыс руб.', 'mln rub.')}</div>
						<div class="price_per_m">Комиссия</div>
					</div>
					<a href="{$url_prefix}/arenda/request/?id={$item.id}" class="btn m m-magenta-fill m-vw">{$lang->get('Оставить заявку', 'Send your request')}</a>
				</div>
				<div class="favorite{if $item.in_favorites} m-added{/if}" {if !empty($item.id) && !empty($moduleUrl)}data-id="{$item.id}" data-url="{$moduleUrl}"{/if}>{fetch file=$path . "favorite.svg"} <span>{fetch file=$path . "favorite.svg"}</span> </div>
			</div>
			<div class="floor-wrap">
				{if !empty($item.tour_url) || !empty($item.tour) || !empty($item.tour_zip)}
					<div class="floor-block m-tour open-tour">
						<div class="main">{$lang->get('3D-тур', '3D tour')}</div>
						{if !empty($cover)}
							<a {if !empty($item.tour_url)}href="{$item.tour_url}" target="_blank"{else}href="#"{/if} class="img-cont">
                                <div class="anim-img"></div>
								<img src="{$cover->getUrl(500, 500, true, false, array('gray', 'brit|0'))}" alt="" />
							</a>
							{if !empty($item.tour_url)}
								<div class="tour-frame">
									<iframe src="{$item.tour_url}" width="100%" height="100%" align="left">
									</iframe>
									<div class="close-tour" title="{$lang->get('Закрыть 3D-тур', 'Close 3D tour')}">{fetch file=$path . "close.svg"}</div>
								</div>
							{elseif !empty($item.tour) || !empty($item.tour_zip)}
                                {?$includeJS.swfobject = "js/lib/swfobject.js"}
								{if !empty($item.tour_zip)}
									{?$includeJS.pano2vr = "js/lib/pano2vr_player.js"}
									{?$includeJS.skin = '../..' . $item.tour_zip->getUrl('relative')|regex_replace:"/\/[a-z,0-9,-,_]*\.xml/":"/skin.js"}
								{/if}
								<div class="tour-frame">
									<div id="flashContent" style="height:100%;"{if !empty($item.tour)} data-swf="{$item.tour->getUrl('relative')}"{/if}{if !empty($item.tour_zip)} data-xml="{$item.tour_zip->getUrl('relative')}"{/if}></div>
									<div class="close-tour" title="{$lang->get('Закрыть 3D-тур', 'Close 3D tour')}">{fetch file=$path . "close.svg"}</div>
								</div>
							{/if}
						{/if}

					</div>
				{/if}

				<div class="floor-block">

				{if !empty($item.video)}
				<div class="floor-block m-video open-video">
				<a target="_blank" href="#" class="btn m-sand m-vw img-cont" style="line-height: 28px;color: #000; margin-bottom: 30px;">видео</a>
												<div class="tour-video">



									<iframe id="video-frame" src="https://www.youtube.com/embed/{$item.video}?autoplay=0&rel=0&theme=dark&enablejsapi=1&showinfo=0"" width="100%" height="100%" align="left">
</iframe>
									<div class="close-video" title="{$lang->get('Закрыть видео', 'Close video')}">{fetch file=$path . "close.svg"}</div>
								</div>
								</div>
								{/if}
					<div class="main">{$lang->get('Планировки', 'Layouts')}</div>
					{if !empty($item.shemes)}{?$schemes = $item.shemes->getImages()}{/if}
					{if !empty($item.shemes) && !empty($schemes)}
						<div class="swiper-container">
							<div class="swiper-wrapper">
								{foreach from=$schemes item=scheme name=scheme_n}
									<div class="cover swiper-slide img-cont open-photo" data-id="{$scheme.id}" style="background:url({$scheme->getUrl(400,400, false, false, array('gray', 'brit|0'))}){if !empty($gravity)} {$gravity[$scheme.gravity]}{/if} no-repeat;  background-size:contain;">
									</div>
								{/foreach}
							</div>
						</div>
						{if !empty($smarty.foreach.scheme_n.total) && $smarty.foreach.scheme_n.total > 1}
							<div class="nav">
								<div class="pagin">1 / 1</div>
								<div class="swiper-pagination"></div>
								<div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
								<div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
							</div>
						{/if}
					{else}
						<a href="{$url_prefix}/arenda/request/?id={$item.id}" class="price">
							<div class="full_price">{$lang->get('по запросу', 'by request')}</div>
							<div class="price_per_m">{$lang->get('Обратитесь к нашим специалистам', 'Ask our experts')}</div>
						</a>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

<div class="item-specs">
	{if !empty($item.features)}
		<div class="item-benefits row a-justify">
			<div class="ben-title main m-vw w2">
				{if ($item.typerk=='Квартира')}
				{$lang->get('Достоинства квартиры', 'Advantages of the appartments')}
				{else}
				{$lang->get('Достоинства коттеджа', 'Advantages of the cottage')}
				{/if}
			</div>
			<div class="ben-list w2">
				{foreach from=$item.features item=benefit}
					<div class="ben-item">{$benefit}</div>
				{/foreach}
			</div>
		</div>
	{/if}
	{?$site_conf = $site_config->get(null, 'global')}
	{if !empty($item.consultant)}
		{?$consultant = $item.consultant}
	{elseif !empty($site_conf.resale_consultant)}
		{if $site_conf.properties.resale_consultant.set == 1}
			{foreach from=$site_conf.resale_consultant item=cons name=cons}
				{?$consultant = $cons}
				{break}
			{/foreach}
		{else}
			{?$consultant = $site_conf.resale_consultant}
		{/if}
	{/if}
	{if !empty($type_properties) || !empty($item.consultant_text) || !empty($consultant)}
		<div class="main-specs row a-justify consultant">
			<div class="w2">
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
			</div>
			<div class="opinion w1">
				{if !empty($item.consultant_text)}
					{?$consultant_text = $item.consultant_text}
				{elseif !empty($site_conf.consultant_text_resale)}
					{?$consultant_text = $site_conf.consultant_text_resale}
				{else}
					{?$consultant_text = ''}
				{/if}
				{if !empty($consultant_text)}
					<div class="quote-icon">{fetch file=$path . "quote.svg"}</div>
					<div class="quote">— {$consultant_text}</div>
					<a href="{$url_prefix}/arenda/request/?id={$item.id}" class="btn m-white">{$lang->get('Задать вопрос', 'Ask a question')}</a>
				{/if}
			</div>
			<div class="person w1">
				{if !empty($consultant)}
					{if !empty($consultant.photo)}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
					{if !empty($consultant.name)}<div class="name">{$consultant.name} {$consultant.surname}</div>{/if}
					{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
					{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
				{/if}
			</div>
		</div>
	{/if}
</div>
{?$empty_description = TRUE}
{if !empty($item.description) && (!empty($item.description.title) || !empty($item.description.annotation) || !empty($item.description.text))}
    {?$empty_description = FALSE}
{/if}
<div class="post-map row a-justify{if $empty_description} m-center{/if}">
	{if !$empty_description}
		<div class="post-col w2 post">
			{if !empty($item.title)}<h2 class="main" title="{$lang->get('Информация о квартире', 'Information about apartment')} {$item.title}">

			{if ($item.typerk=='Квартира')}{$lang->get('Информация о квартире', 'Information about apartment')}
		{else}
		{$lang->get('Информация о коттедже', 'Information about cottage')}
			{/if}

			<br>{$item.title}</h2>{/if}
			{if !empty($item.description.title)}<div class="title"><span>{$item.description.title|html}</span></div>{/if}
			{if !empty($item.description.annotation)}<h3 class="descr">{$item.description.annotation|html}</h3>{/if}
			{if !empty($item.description.text)}
				<div class="text">
					{$item.description.text|html}
				</div>
			{/if}
		</div>
	{/if}
	<div class="contacts-map w2">
		{if !empty($item.address_coords)}
			<div class="map-big">
				<div class="close-map" title="{$lang->get('Закрыть карту', 'Close map')}">{fetch file=$path . "close.svg"}</div>
				<div class="map" data-coords="{$item.address_coords}"></div>
				<div class="infoblock-content a-hidden">
					<div class="map-item-content">
						<div class="item-type main">{if ($item.typerk=='Квартира')}{$lang->get('Элитная квартира', 'Elite apartment')}{else}{$lang->get('Элитный коттедж', 'Elite cottage')}{/if}</div>
						<div class="item-title descr-big">{$item.title}</div>
						<div class="address">{$item.address}</div>
					</div>
				</div>
				<div class="infra-markers">
					{foreach from=$item.infra item=infra}
						{if !empty($infra.address_coords)}
							<div class="marker" data-coords="{$infra.address_coords}" data-title="{if !empty($infra.title)}{$infra.title}{/if}"{if !empty($infra.type)} data-img="/img/infra/{$infra.properties.type.value_key}.png{/if}"></div>
						{/if}
					{/foreach}
				</div>
			</div>
		{/if}
		<div class="map-small">
			<h2 class="main-top main" title="{$lang->get('Расположение и окружение квартиры', 'Apartment location and surroundings')}">{if ($item.typerk=='Квартира')}{$lang->get('Расположение<br>и окружение квартиры', 'Apartment location<br>and surroundings')|html}{else}{$lang->get('Расположение<br>и окружение коттеджа', 'Cottage location<br>and surroundings')|html}{/if}</h2>
			<div class="titles">
				{if !empty($item.district.title)}<div class="distr title"><span>{$item.district.title}</span></div>{/if}
				{if !empty($item.address)}<div class="address descr-big"><span>{$item.address}</span></div>{/if}
			</div>
			{if !empty($item.address_coords)}
				<div class="open-map">
					<div class="btn m-magenta-fill">
           <span class="open-map-cover"></span>
          {fetch file=$path . "expand.svg"} {$lang->get('Открыть карту', 'Enlarge Map')}
          </div>
					<div class="marker"></div>
					<div class="map-lock"></div>
					<div class="map-wrap">
						<div class="map" data-coords="{$item.address_coords}"></div>
					</div>
				</div>
			{/if}
			<div class="map-info row a-justify">
				<div class="w2">
					{if !empty($item.metro) && count($item.metro)}
						<div class="info-block">
							<div class="main">{$lang->get('Станция метро', 'Subway Station')}</div>
							{foreach from=$item.metro item=metro}
								<div class="val">{$metro.variant_title}</div>
							{/foreach}
						</div>
					{/if}
					{if !empty($item.center_distance) || !empty($item.center_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Центр петербурга', 'City Center')}</div>
							<div class="val">{$item.center_distance} {if !empty($item.center_distance) && !empty($item.center_time_bycar)} — {/if}{$item.center_time_bycar} </div>
						</div>
					{/if}
				</div>
				<div class="w2">
					{if !empty($item.airport_distance) || !empty($item.airport_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Аэропорт пулково', 'Pulkovo Airport')}</div>
							<div class="val">{$item.airport_distance} {if !empty($item.airport_distance) && !empty($item.airport_time_bycar)} — {/if}{$item.airport_time_bycar} </div>
						</div>
					{/if}
					{if !empty($item.kad_distance) || !empty($item.kad_time_bycar)}
						<div class="info-block">
							<div class="main">{$lang->get('Кольцевая автодорога', 'Ring Road')}</div>
							<div class="val">{$item.kad_distance} {if !empty($item.kad_distance) && !empty($item.kad_time_bycar)} — {/if}{$item.kad_time_bycar} </div>
						</div>
					{/if}
				</div>
			</div>
			{if !empty($item.infra_text)}
			<div class="infra">
				<div class="distr title"><span>{$lang->get('Инфраструктура', 'Infrastructure')}</span></div>
				<p>{$item.infra_text}</p>
			</div>
			{/if}
		</div>
	</div>
</div>

<div class="make-request">
	{?$checkString = time()}
	{?$checkStringSalt = $checkString . $hash_salt_string}

	<form action="/feedback/makeRequest/" class="request-form user-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<input type="hidden" name="check_string" value="" />
		<input type="hidden" name="hash_string" value="" />
		<input type="hidden" name="feedbackType" value="{$form_type}">
		<input type="hidden" name="apartment" value="{$item.id}" />
		<div class="main m-vw open-block">{if ($item.typerk=='Квартира')}{$lang->get('Отправьте заявку на просмотр квартиры', 'Fill the form to send your booking request')}{else}{$lang->get('Отправьте заявку на просмотр коттеджа', 'Fill the form to send your booking request')}{/if}</div>
		<div class="open-block a-justify">
			<label class="field">
				<div class="f-row">
					<div class="f-title">
						<span>{$lang->get('Имя', 'Name')}</span>
						<span class="slash"></span>
					</div>
					<div class="f-input">
						<input type="text" name="author" />
					</div>
					<div class="f-errors a-hidden">
						{$lang->get('Обязательное поле', 'Required')}
					</div>
				</div>
			</label>
			<label class="field">
				<div class="f-row">
					<div class="f-title">
						<span>{$lang->get('Тел.', 'Tel.')}</span>
						<span class="slash"></span>
					</div>
					<div class="f-input">
						<input type="tel" name="phone" value="" />
					</div>
					<div class="f-errors a-hidden">
						{$lang->get('Обязательное поле', 'Required')}
					</div>
				</div>
			</label>
			<label class="field">
				<div class="f-row">
					<div class="f-title">
						<span>E-mail</span>
						<span class="slash"></span>
					</div>
					<div class="f-input">
						<input type="email" name="email" />
					</div>
					<div class="f-errors a-hidden">
						{$lang->get('Обязательное поле', 'Required')}
					</div>
				</div>
			</label>

			<div class="buttons">
				<button class="btn m-sand">{$lang->get('Отправить', 'Send')}</button>
			</div>
		</div>
        <div class="open-block a-justify agree-wrap">
			<label class="field checkbox ">
				<div class="f-row">
					<div class="f-input">
						<input id="agree" type="checkbox" name="agree" checked="checked"/>
						<label for="agree"><div></div><span>{$lang->get('Принимаю', 'I agree to the ')}</span>
							<a href="/privacy_policy/">
								<span>{$lang->get('соглашение на обработку персональных данных', 'processing of personal data')}</span>
							</a>
						</label>
					</div>
					<div class="f-errors a-hidden">
						{$lang->get('Нужно согласиться', 'Need agree')}
					</div>
				</div>
			</label>
		</div>
		<div class="sended-block">
			<div class="main">{$lang->get('Заявка отправлена', 'Message sent')}</div>
			<div class="small-descr">
				{$lang->get('Ваша заявка успешно отправлена<br>консультантам агентства<br>недвижимости М16. Спасибо за ваше<br>обращение!', 'Your request is successfully sent<br>to conultants of M16 real estate agency.<br>Thank you for contacting us!')|html}
			</div>
		</div>
	</form>
</div>

<div class="qr-block-wrap">
	<div class="qr-block">
		<div class="qr">
			<div class="qr-wrap">
				<img src="https://chart.googleapis.com/chart?cht=qr&chs=235x235&chld=L|2&chl=https://{$smarty.server.SERVER_NAME}{$item->getUrl()}" alt="" />
			</div>
		</div>
		<div class="main">{$lang->get('Откройте страницу на смартфоне', 'Open this page on your smartphone')}</div>
		<div class="small-descr">{$lang->get('Нажмите, чтобы увеличить QR-код', 'Press it to enlarge QR code')}</div>
	</div>
</div>

<div class="swipe-wrap m-see-more">
	{if !empty($similar_objects)}
		<h2 class="main m-vw" title="{$lang->get('Похожие предложения', 'Similar offers')}">{$lang->get('Похожие предложения', 'Similar offers')}</h2>
		<div class="title">{$lang->get('Стоит присмотреться', 'It is worth a closer look')}</div>
		<div class="swiper-container">
			<div class="w4 swiper-wrapper">
				{foreach from=$similar_objects item=sim name=sim_n}
					{?$cover = !empty($sim.gallery) ? $sim.gallery->getCover() : null}
					{?$delim = ldelim . "!" . rdelim}
					{?$sim_title = $sim.title|replace:$delim:' '}
					{?$url = !empty($sim->getUrl()) ? $sim->getUrl() : null}
					<div class="swiper-slide m-vw flat-wrap{if $smarty.foreach.sim_n.total == 1} m-center{/if}">
						<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url('/img/veil.png'), url({!empty($cover) ? $cover->getUrl(940, 650) : ''}){if !empty($gravity)} {$gravity[$image.gravity]}{/if}; background-size:cover;"></a>
						<div class='params'>
							<div class="main m-vw">{$item.typerk}</div>
							{if !empty($item.bed_number)}<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>{/if}
							{if !empty($sim.address)}<div class="descr">{$sim.address}</div>{/if}
							{if !empty($sim.area_all)}
								<div class="area">{$sim.area_all}</div>
							{/if}
							{if !empty($sim.overhang)}
								<div class="small-descr">
									{$lang->get('Есть ', 'With ')}
									{foreach from=$sim.overhang item=overhang name=overhang_n}
										{$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
									{/foreach}
								</div>
							{/if}
							{if !empty($url)}<a href="{$url}" class="btn m-sand m-vw">{$lang->get('Выбрать', 'Choose')}</a>{/if}
						</div>
					</div>
				{/foreach}
			</div>
		</div>
		<div class="swiper-button-prev{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		<div class="swiper-button-next{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
	{/if}
	<div class="descr">{$lang->get('А почему бы не изучить предложения в новостройках? Отличное вложение денег!', 'Why not check some offers in buildings under construction?')}</div>
	<a href="{$url_prefix}/real-estate/" class="btn m-light-magenta">{$lang->get('Смотреть квартиры в строящихся домах', 'Search in new objects')}</a>
</div>
{include file='/components/itemMicroMark.tpl'}
