{?$delim = ldelim . "!" . rdelim}
{?$title_arr = $delim|explode:$item.title}
{?$title = $item.title|replace:$delim:' '}
{if $request_segment.key == 'ru'}
	{?$pageTitle = $title|strip_tags . ' | М16-Недвижимость'}
	{?$pageDescription = $title|strip_tags . ' — большой выбор элитной недвижимости в новостройках и на вторичном рынке для самых взыскательных клиентов от агентства недвижимости Вячеслава Малафеева'}
{else}
	{?$pageTitle = $title|strip_tags . ' | M16 Real Estate Agency'}
	{?$pageDescription = $title|strip_tags . ' — a wide range of luxury real estate in new buildings and on the secondary market for the most demanding clients at M16 Real Estate Agency of Vyacheslav Malafeyev'}
{/if}

{if !empty($item.gallery)}{?$gallery = $item.gallery->getImages()}{/if}
{?$og_meta = array()}
{?$og_meta['type'] = 'website'}
{?$og_meta['site_name'] = 'М16-Недвижимость'}
{?$og_meta['title'] = $pageTitle}
{?$og_meta['description'] = $pageDescription}
{?$og_meta['url'] = $page_url}
{?$og_meta['locale'] = 'ru_RU'}

{if !empty($gallery)}
	{foreach from=$gallery item=image  name=og_images}
		{if iteration == 3}
			{?$og_meta['image'] = $image->getCleanUrl()}
			{?$og_meta['width'] = $image->getWidth()}
			{?$og_meta['height'] = $image->getHeight()}
			{break}
		{/if}
	{/foreach}
{/if}


<div class="top-bg" id="site-top" itemscope itemtype="http://schema.org/Place">
	{if !empty($item.gallery)}{?$cover = $item.gallery->getCover()}{/if}
	<div class='bg-img' style='background: url("/img/veil.png"), url({!empty($cover) ? $cover->getUrl() : ''}); background-size:cover;'></div>

    <div class="breadcrumbs">
		<ul itemscope itemtype="http://schema.org/BreadcrumbList">
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a itemprop="item" href="/"><span itemprop="name">Главная</span></a>
			<meta itemprop="position" content="1" />
			</li>
			<span class="slash"></span>
			<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<a itemprop="item" href="/real-estate/"><span itemprop="name">Строящаяся недвижимость</span>
			</a><meta itemprop="position" content="2" />
			</li>
			<span class="slash"></span>
			<li>
				{? $cat = ''}
                {if $request_segment.key == 'ru'}{? $cat = 'ЖК '}{/if}
				<span>{$cat . $title}</span>

			</li>
		</ul>
	</div>

    <div class="site-top">
		<div class="main">{$lang->get('жилой комплекс','residential complex')}</div>
		<h1 class="title" title="{$title}" itemtype="name"><span>{$title_arr[0]}</span>{if !empty($title_arr[1])}<br>{$title_arr[1]}{/if}</h1>
		{if !empty($item.snippet)}<div class="descr">{$item.snippet|html}</div>{/if}
		{if !empty($item.icon)}
			<div class="icon-block">
                {if is_array($item.icon)}
                    {foreach from=$item.icon item=icon}
                        {if !empty($icon)}<div class="skew m-sand-skew">{$icon}</div>{/if}
                    {/foreach}
                {else}
                    <div class="skew m-sand-skew">{$item.icon}</div>
                {/if}
			</div>
		{/if}
		{if !empty($item.flats_for_sale_count)}
			{if !empty($item.sheme_get)}
				{?$url_for_flat = $item->getUrl() . "scheme/"}
			{else}
				{if $item.flats_for_sale_count == '1'}
					{foreach from=$item.flats_for_sale item=one_flat}
						{?$url_for_flat = $one_flat->getUrl()}
					{/foreach}
				{else}
					{?$url_for_flat = $item->getUrl() . "apartments/"}
				{/if}
			{/if}
			<a href="{$url_for_flat}" class="choose-apt btn m-sand">{$lang->get('Выбрать квартиру','Choose apartments')}</a>
			{*<a href="{$url_for_flat}" class="splited-block">
				<div class="splited-border m-left"><span class="splited-inner">{if $item.flats_for_sale_count == '1'}{$lang->get('Посмотреть', 'Show')}{else}{$lang->get('Выбрать из','Choose from')}{/if}</span></div>
				<div class="splited-center">{$item.flats_for_sale_count}</div>
				<div class="splited-border m-right">
					<span class="splited-inner">
						{$lang->get($item.flats_for_sale_count|plural_form:'квартиру':'квартиры':'квартир':false, $item.flats_for_sale_count|plural_form:'apartment':'apartments':'apartments':false)}
					</span>
				</div>
			</a>*}
		{/if}
	</div>
</div>

{include file='/components/main_menu.tpl' complex=$item}


{if !empty($gallery) && count($gallery) > 1}
	<div class="gallery-tiles" id="gallery-tiles">
		<div class="gallery-big">
			<div class="close-photo" title="{$lang->get('Закрыть изображение', 'Close photo')}">{fetch file=$path . "close.svg"}</div>
			<div class="swiper-container">
				<div class="swiper-wrapper">
					{foreach from=$gallery item=image name=gallery_swiper}
						<div class="swiper-slide img-{iteration}{if iteration > 1} delay-img{/if}"
							{if iteration > 1} data-bg-img='{$image->getUrl(1900,950)}'{/if}
							style="{if iteration < 2}background-image:url({$image->getUrl(1900,950)});{/if}background-position:{if !empty($gravity)} {$gravity[$image.gravity]}{/if}; background-size:cover;">
						</div>
						{*<a href="{if !empty($url)}{$url}{/if}"
						   {if iteration > 1} data-bg-img='{$image->getUrl(372,270)}'{/if}
							class='cover{if iteration > 1} delay-img{/if}'
							style="{if iteration < 2}background-image: url({$image->getUrl(372,270)});{/if}{if !empty($gravity)}background-position:{$gravity[$image.gravity]}{/if} background-size:cover;">
						</a>*}
					{/foreach}
				</div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-prev"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
				<div class="swiper-button-next"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
			</div>
		</div>
		<div class="gallery-small tiles">
			<div class="info">
				<div class="title">{$lang->get('фотогалерея', 'Photo gallery')}</div>
				<div class="count">
					<div class="count-num">{$smarty.foreach.gallery_swiper.total}</div>
					<div class="count-text">{$lang->get($smarty.foreach.gallery_swiper.total|plural_form:'вид':'вида':'видов':false, $smarty.foreach.gallery_swiper.total|plural_form:'view':'views':'views':false)}</div>
				</div>
				<div class="open-photo btn m-light-magenta">{$lang->get('Смотреть', 'Take a look')}</div>
			</div>

			{foreach from=$gallery item=image name=gallery}
				{if iteration > 7}{break}{/if}
				<div class="open-photo photo-cont photo-{iteration}" data-id="{iteration}">
					<div style="background:url({$image->getUrl(1000,500)}){if !empty($gravity)} {$gravity[$image.gravity]}{/if}; background-size:cover;"></div>
				</div>
			{/foreach}
		</div>
	</div>
{/if}
<div class="contacts-map" id="contacts-map" itemscope itemtype="http://schema.org/Place">
	{if !empty($item.address_coords)}
		<div class="map-big">
			<div class="close-map" title="{$lang->get('Закрыть карту', 'Close map')}">{fetch file=$path . "close.svg"}</div>
			<div class="map" data-coords="{$item.address_coords}"></div>
			<div class="infoblock-content a-hidden">
				<div class="map-item-content">
					<div class="item-type main">{$lang->get('Жилой комплекс', 'Residential Complex')}</div>
					<div class="item-title descr-big">{$title}</div>
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
		<div class="page-center">
			<h2 class="main-top main" title="{$lang->get('Расположение и окружение объекта', 'OBJECT LOCATION AND SURROUNDINGS')}">{$lang->get('Расположение и окружение объекта', 'OBJECT LOCATION AND SURROUNDINGS')}</h2>
			<div class="titles">
				{if !empty($item.district.title)}<div class="distr title"><span>{$item.district.title}</span></div>{/if}
				
			</div>
			{if !empty($item.address_coords)}
				<div class="open-map">
					<div class="btn m-magenta-fill">
            <span class="open-map-cover"></span>
            {fetch file=$path . "expand.svg"} {$lang->get('Открыть большую карту', 'Open Large Map')}
          </div>
					<div class="marker"></div>
					<div class="map-lock"></div>
					<div class="map-wrap">
						<div itemprop="hasMap" class="map" data-coords="{$item.address_coords}"></div>
					</div>
				</div>
			{/if}
			<div class="map-info row">
				<div class="w1 a-left">
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
				<div class="w1 a-right">
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
				<div class="w2">
					{if !empty($item.district)}
						{if !empty($item.district.post.annotation)}<p class="quote">{$item.district.post.annotation}</p>{/if}
						{if !empty($item.district.post) && $item.district.post.status == 'close' && $item.district.status == '3'}
							<a href="{$item.district->getUrl($request_segment.id)}" class="about-url">— <span>{$lang->get('Узнайте больше об этом районе','Find out more about the area')}</span></a>
						{/if}
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

{?$options_small = array()}
{?$options_big = array()}
{if !empty($item.flats_count) || !empty($item.flats_for_sale_count)}
	{capture assign=option_flats_count}
		<div class="w1 a-inline-cont">
			{if !empty($item.flats_count)}
				<div>
					<div class="big-opt m-right">{$item.flats_count} </div>
					<div class="opt-descr">{$lang->get('Квартир в доме','Apartments')}</div>
				</div>
			{/if}
			{if !empty($item.flats_count) && !empty($item.flats_for_sale_count)}
				<div>
					<div class="big-opt">/</div>
				</div>
			{/if}
			{if !empty($item.flats_for_sale_count)}
				<a href="{$url_for_flat}">
					<div class="big-opt m-sand m-left"> {$item.flats_for_sale_count}</div>
					<div class="opt-descr">{$lang->get('В продаже', 'For sale')}</div>
				</a>
			{/if}
		</div>
	{/capture}
	{?$options_small[] = $option_flats_count}
{/if}
{if !empty($item.properties.price_meter_from.value)}
	{capture assign=option_price_meter}
		<div class="w1">
			<div class="big-opt">{$item.properties.price_meter_from.value}<span class="m-sand">+</span></div>
			<div class="opt-descr">{$lang->get('Тысяч рублей за м','ths rub. per m')}²</div>
		</div>
	{/capture}
	{?$options_small[] = $option_price_meter}
{/if}
{if !empty($item.properties.app_area.value)}
	{capture assign=option_app_area}
		<div class="w2 a-inline-cont">
			<div class="opt-descr m-left">{$lang->get('Площади<br />квартир', 'Flats<br />area')|html}</div>
			<div class="big-opt">{$item.properties.app_area.value}<span class="small-opt"> {$lang->get('м', 'm')}²</span></div>
		</div>
	{/capture}
	{?$options_big[] = $option_app_area}
{/if}
{if !empty($item.properties.ceiling_height.value)}
	{capture assign=option_ceiling_height}
		<div class="w1">
			<div class="big-opt">{$item.properties.ceiling_height.value}</div>
			<div class="opt-descr">{$lang->get('Высота потолков, М', 'Ceiling height, M')}</div>
		</div>
	{/capture}
	{?$options_small[] = $option_ceiling_height}
{/if}
{if !empty($item.properties.number_storeys.value)}
	{capture assign=option_number_storeys}
		<div class="w1">
			<div class="big-opt">{$item.properties.number_storeys.value}</div>
			<div class="opt-descr">{$lang->get($item.properties.number_storeys.value|plural_form:'Этаж':'Этажа':'Этажей':false, $item.properties.number_storeys.value|plural_form:'Floor':'Floors':'Floors':false)}</div>
		</div>
	{/capture}
	{?$options_small[] = $option_number_storeys}
{/if}

{if !empty($item.properties.construction_stage.value_key) && $item.properties.construction_stage.value_key == 'under_construction'}
	{?$complete = Array('first' => '1','second' => '2','third' => '3','fourth' => '4')}
	{?$complete_ending = Array('first' => '1','second' => '2','third' => '3','fourth' => '4')}
	{if !empty($item.properties.complete.value_key) && !empty($complete[$item.properties.complete.value_key]) && $item.complete_year}
		{capture assign=option_complete}
			<div class="w2 m-end-date{if $request_segment.key != 'ru'} m-eng{/if}">
				<div class="date-top a-inline-cont">
					<div class="opt-descr">{$lang->get('Срок завершения<br />строительства','Completion <br />of construction')|html}</div>
				</div>
				<div class="date-bottom">
					<div class="small-opt"><span class="big-opt m-complete">{$complete[$item.properties.complete.value_key]}<i>-{if $request_segment.key == 'ru'}й{else}{$item.properties.complete.value_key|substr:-2:2}{/if}</i></span> {$lang->get('квартал', 'quarter')} <span class="big-opt">{$item.complete_year}</span></div>
				</div>
			</div>
		{/capture}
		{?$options_big[] = $option_complete}
	{/if}
{else}
	{capture assign=option_complete}
		<div class="w2 m-end-date">
			<div class="big-opt">{$lang->get('ДОМ СДАН','Complete')}</div>
			<div class="opt-descr">{$lang->get('Строительство завершено', 'Сonstruction process')}</div>
		</div>
	{/capture}
	{?$options_big[] = $option_complete}
{/if}

{if count($options_small) || count($options_big)}
	<div class="options" id="options">
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


{?$site_conf = $site_config->get(null, 'global')}
{if !empty($item.consultant)}
	{?$consultants = $item.consultant}
{elseif !empty($site_config.real_estate_consultant)}
	{?$consultants = $site_config.real_estate_consultant}
{else}
	{?$consultants = null}
{/if}
{if !empty($consultants)}
	<div class="consultant">
		{foreach from=$consultants item=consultant name=cons}
			<div class="person{if $smarty.foreach.cons.iteration % 2 == 0} m-right{else} m-left{/if}" style="display:none;">
				{if !empty($consultant.photo) && !empty($consultant.photo->getUrl())}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
				{if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
				{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
				{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
			</div>
		{/foreach}
		<div class="info m-center">
			<div class="title"><span>{$lang->get('Консуль&shy;тация','Сonsul&shy;tation')|html}</span></div>
			<div class="big-title">{$lang->get('M16','M16')}</div>
			<a href="{$url_prefix}/real-estate/request/?id={$item.id}" class="btn m-magenta-fill">{$lang->get('Оставить заявку','Send your request')}</a>
			<div id="paramsblock">
			</div>
		</div>
	</div>
{/if}


{if !empty($item.concept) || !empty($item.materials) || !empty($item.engineer_solution) || !empty($item.public_space) || !empty($item.parking) || !empty($item.progress)}
<div class="art-tiles">
	{if !empty($cover)}
		<div class="article art-cover">
			<div class="img" style="background:url({$cover->getUrl(500,500)}){if !empty($gravity)}{if !empty($gravity)} {$gravity[$cover.gravity]}{/if}{/if}; background-size:cover;"></div>
		</div>
	{/if}
	{if !empty($item.consultant_text)}
		{?$consultant_text = $item.consultant_text}
	{elseif !empty($site_conf.consultant_text)}
		{?$consultant_text = $site_conf.consultant_text}
	{else}
		{?$consultant_text = ''}
	{/if}
	{if !empty($consultants)}
		{foreach from=$consultants item=first_consultant name=first}
			{?$first_consultant_title = $first_consultant.title}
			{?$first_consultant_appointment = $first_consultant.appointment}
			{break}
		{/foreach}
	{/if}
	{if !empty($consultant_text)}
		<div class="article art-info m-info">
			<div class="content">
				<i></i>
				<div class="quote-icon">{fetch file=$path . "quote.svg"}</div>
				<div class="quote">— {$consultant_text}</div>
				{if !empty($first_consultant_title)}<div class="author main m-vw">{$first_consultant_title}</div>{/if}
				{if !empty($first_consultant_appointment)}<div class="function">{$first_consultant_appointment}</div>{/if}
			</div>
		</div>
	{/if}
	{?$art_iteration = 0}
	{if !empty($item.concept)}
        {?$has_text = FALSE}
        {foreach from=$item.concept item=concept}
            {if !empty($concept.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}concept/" class="article art-tile art-{$art_iteration}">
                {foreach from=$item.concept item=concept}
                    {if !empty($concept.gallery)}{?$c_cover = $concept.gallery->getCover()}{/if}
                    {if !empty($c_cover)}<div class='img' style="background:url({$c_cover->getUrl(600,1000,false,true,array('ds|30'))}){if !empty($gravity)} {$gravity[$c_cover.gravity]}{/if}; background-size:cover;"></div>{/if}
                    {break}
                {/foreach}
                <div class="hatch"></div>
                <div class="num">{$art_iteration}.</div>
                <div class="content">
                    <div class="btn m-black">{$lang->get('Концепция','Concept')}</div>
                </div>
            </a>
        {/if}
	{/if}
	{if !empty($item.materials)}
        {?$has_text = FALSE}
        {foreach from=$item.materials item=materials}
            {if !empty($materials.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}materials/" class="article art-tile art-{$art_iteration}">
                {foreach from=$item.materials item=arch}
                    {if !empty($arch.gallery)}{?$c_arch = $arch.gallery->getCover()}{/if}
                    {if !empty($c_arch)}<div class='img' style="background:url({$c_arch->getUrl(950,475,false,true,array('ds|30'))}){if !empty($gravity)} {$gravity[$c_arch.gravity]}{/if}; background-size:cover;"></div>{/if}
                    {break}
                {/foreach}
                <div class="hatch"></div>
                <div class="num">{$art_iteration}.</div>
                <div class="content">
                    <div class="btn m-black">{$lang->get('Архитектура','Architecture')}</div>
                </div>
            </a>
        {/if}
	{/if}
	{if !empty($item.engineer_solution)}
        {?$has_text = FALSE}
        {foreach from=$item.engineer_solution item=engineer_solution}
            {if !empty($engineer_solution.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}engineer_solution/" class="article art-tile art-{$art_iteration}">
                {foreach from=$item.engineer_solution item=eng}
                    {if !empty($eng.gallery)}{?$c_eng = $eng.gallery->getCover()}{/if}
                    {if !empty($c_eng)}<div class='img' style="background:url({$c_eng->getUrl(950,475,false,true,array('ds|30'))}){if !empty($gravity)} {$gravity[$c_eng.gravity]}{/if}; background-size:cover;"></div>{/if}
                    {break}
                {/foreach}
                <div class="hatch"></div>
                <div class="num">{$art_iteration}.</div>
                <div class="content">
                    <div class="btn m-black">{$lang->get('Инженерия','Engineering')}</div>
                </div>
            </a>
        {/if}
	{/if}
	{if !empty($item.public_space)}
        {?$has_text = FALSE}
        {foreach from=$item.public_space item=public_space}
            {if !empty($public_space.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}public_space/" class="article art-tile art-{$art_iteration}">
                {foreach from=$item.public_space item=pub}
                    {if !empty($pub.gallery)}{?$c_pub = $pub.gallery->getCover()}{/if}
                    {if !empty($c_pub)}<div class='img' style="background:url({$c_pub->getUrl(950,475,false,true,array('ds|30'))}){if !empty($gravity)} {$gravity[$c_pub.gravity]}{/if}; background-size:cover;"></div>{/if}
                    {break}
                {/foreach}
                <div class="hatch"></div>
                <div class="num">{$art_iteration}.</div>
                <div class="content">
                    <div class="btn m-black">{$lang->get('Обустройство','Equipment')}</div>
                </div>
            </a>
        {/if}
	{/if}
	{if !empty($item.parking)}
        {?$has_text = FALSE}
        {foreach from=$item.parking item=parking}
            {if !empty($parking.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}parking/" class="article art-tile art-{$art_iteration}">
                {foreach from=$item.parking item=parking}
                    {if !empty($parking.gallery)}{?$c_parking = $parking.gallery->getCover()}{/if}
                    {if !empty($c_parking)}<div class='img' style="background:url({$c_parking->getUrl(950,475,false,true,array('ds|30'))}){if !empty($gravity)} {$gravity[$c_parking.gravity]}{/if}; background-size:cover;"></div>{/if}
                    {break}
                {/foreach}
                <div class="hatch"></div>
                <div class="num">{$art_iteration}.</div>
                <div class="content">
                    <div class="btn m-black">{$lang->get('Паркинг','Parking')}</div>
                </div>
            </a>
        {/if}
	{/if}
	{if !empty($item.progress)}
        {?$has_text = FALSE}
        {foreach from=$item.progress item=progress}
            {if !empty($progress.text)}
                {?$has_text = TRUE}
                {break}
            {/if}
        {/foreach}
        {if (!empty($has_text))}
            {?$art_iteration++}
            <a href="{$item->getUrl()}progress/" class="article art-tile art-{$art_iteration} m-progress">
                <div class="num">{fetch file=$path . "progress.svg"}</div>
                <div class="content">
                    <div class="title"><span>{$lang->get('Ход стройки','Building Process')}</span></div>
                    <div class="btn m-black m-vw">{$lang->get('Читать', 'Read')}</div>
                </div>
            </a>
        {/if}
	{/if}
</div>
{/if}

{include file="/components/about.tpl"}
{if $fullpage==1}
<div style="display:none">
<span>Цена: от {$min_price} до {$max_price}</span>
</div>
<div itemprop="Product" itemscope="" itemtype="http://schema.org/Product">
<meta itemprop="name" content="{$pageTitle}">
<meta itemprop="description" content="{$pageDescription}">
<div itemprop="offers" itemscope="" itemtype="http://schema.org/AggregateOffer">
	{if isset($item['rating']) && $item['rating'] != 0}
		{$voted = (isset($isVotedRating) && $isVotedRating == true) ? true : false}
	{else}
		{$voted = false}
	{/if}

	{if isset($min_price) && !empty($min_price)}
		<meta itemprop="lowPrice" content='{$f_min_price}'>
	{/if}
    {if isset($max_price) && !empty($max_price)}
		<meta itemprop="highPrice" content="{$f_max_price}">
    {/if}
	<meta itemprop="priceCurrency" content="RUB">
</div>
<div id="complex_rating" class="complex-rating-wrapper text-center" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
			<div id="rating" class="rating" data-object="{$item['id']}" data-rating="{$item['rating']}"
				 data-markscount="{$marksCount}" data-maxmark="{$maxMark}" data-voted="{$voted}"></div>
			<p class="descr">Рейтинг жилого комплекса</p>
			<span id="rating-info">
				{$marksCount} {$lang->get('оценок','marks')}, средняя {$item['rating']} из {$maxMark} {if $voted == true}{$lang->get(', вы уже поставили оценку', ', вы уже поставили оценку')}{/if}
			</span>
	        {*  перевести перевод на друг локации(языки)*}
			<meta itemprop="ratingValue" content="{$item['rating']}">
			<meta itemprop="reviewCount" content="{$marksCount}">
			<meta itemprop="bestRating" content="5">
			<meta itemprop="worstRating" content="0">
		</div>
</div>
{/if}
{if !empty($item.flats_for_sale)}
	<div class="swipe-wrap" id='flats'>
		<h2 class="title" title="{$lang->get('Квартиры в доме','Apartments in')} {$title}"><span>{$lang->get('Квартиры в доме','Apartments in')}</span><br>{$title}</h2>
		{if !empty($item.flats_for_sale_count)}
			<div class="choose-buttons">
				<a href="{$item->getUrl()}scheme/" class="btn m-white">{$lang->get('Выбрать на плане дома','Choose on building scheme')}</a>
				<span class="slash"></span>
				<a href="{$item->getUrl()}apartments/" class="btn m-white">{$lang->get('Выбрать по параметрам','Parameter search')}</a>
			</div>
		{/if}
		<div class="swiper-container">
			<div class="w4 swiper-wrapper">
				{?$iteration_flat = 0}
				{foreach from=$item.flats_for_sale item=flat name=name_n}
					{?$floor = $flat->getParent()}
					{?$corpus = $floor->getParent()}
					{if !empty($flat.shemes)}{?$gallery = $flat.shemes->getImages()}{/if}
					{?$url = !empty($flat->getUrl()) ? $flat->getUrl() : null}
					{?$cover = !empty($flat.shemes) ? $flat.shemes->getCover() : null}
					{if !(!(iteration % 2 == 0) && $smarty.foreach.name_n.last)}
						<div class="swiper-slide flat-wrap m-vw{if $smarty.foreach.name_n.total == 1} m-center{/if}">
							{if !empty($cover)}
                                <div class="cover">
								    <a href="{!empty($url) ? $url : ''}" style="background:url({$cover->getUrl(428,'', false, false, array('gray', 'brit|0'))}) center no-repeat; background-size:contain;"></a>
                                </div>
                            {/if}
							<div class='params'>
								<div class="main m-vw">{$lang->get('Квартира', 'Apartment')}</div>
								<div class="title"><span>{$lang->get($flat.bed_number|plural_form:'спальня':'спальни':'спален', $flat.bed_number|plural_form:'bedroom':'bedrooms':'bedrooms')}</span></div>
								<div class="descr">{if !empty($corpus.title)}{$lang->get(' Корпус', 'Building')} {$corpus.title}{/if}{if !empty($corpus.title) && !empty($floor.title)} • {/if}{if !empty($floor.title)}{$lang->get('Этаж', 'Floor')} {$floor.title}{/if}</div>
								<div class="area">{$flat.properties.area_all.value|html} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
								{if !empty($url)}<div class="bottom"><a href="{$url}" class="btn m-dark-magenta">{$lang->get('Изучить','In detail')}</a></div>{/if}
							</div>
						</div>
						{?$iteration_flat++}
					{/if}
				{/foreach}
			</div>
		</div>
		<div class="swiper-button-prev{if !empty($iteration_flat) && $iteration_flat < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		<div class="swiper-button-next{if !empty($iteration_flat) && $iteration_flat < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
	</div>
{/if}
{if !empty($filter_data)}
	<div class="bedrooms-wrap">
		<h2 class="main" title="{$lang->get('Число спален — площадь квартир','Bedrooms number — Apartments area')}">{$lang->get('Число спален — площадь квартир','Bedrooms number — Apartments area')}</h2>
		{foreach from=$filter_data item=bedroom}
			<a href="{$item->getUrl()}apartments/?bed_number[]={$bedroom['bedroom_count']}" class="btn m-bedroom">
				<span class="count">{$bedroom['bedroom_count']}{$bedroom['bedroom_count'] == 5 ? ' +' : ''}</span>
				{($bedroom.area_min != $bedroom.area_max ? round($bedroom.area_min).'—'.round($bedroom.area_max) : round($bedroom.area_max)) . ($request_segment.key == 'ru' ? ' м<sup>2</sup>' : ' m<sup>2</sup>')|html}
			</a>
		{/foreach}
	</div>
{/if}


<div class="swipe-wrap m-see-more">
	{if !empty($similar_objects)}
		<h2 class="main m-vw" title="{$lang->get('Похожие предложения','Similar offers')}">{$lang->get('Похожие предложения','Similar offers')}</h2>
		<div class="title">{$lang->get('Стоит присмотреться','It is worth a closer look')} </div>
		<div class="swiper-container">
			<div class="w4 swiper-wrapper">
				{foreach from=$similar_objects item=sim name=sim_n}
					{?$cover = !empty($sim.gallery) ? $sim.gallery->getCover() : null}
					{if !empty($sim.title)}
						{?$delim = ldelim . "!" . rdelim}
						{?$sim_title = $sim.title|replace:$delim:' '}
					{/if}
					{?$url = !empty($sim->getUrl()) ? $sim->getUrl() : null}
					<div class="swiper-slide m-vw flat-wrap{if $smarty.foreach.sim_n.total == 1} m-center{elseif !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} m-margin{/if}">
						<a href="{if !empty($url)}{$url}{/if}" class='cover' {if !empty($cover)}style="background: url('/img/veil.png'), url({$cover->getUrl(940, 650)}){if !empty($gravity)} {$gravity[$cover.gravity]}{/if}; background-size:cover;"{/if}>
						</a>
						<div class='params'>
							<div class="main m-vw">{$lang->get('Жилой комплекс','Residential Complex')}</div>
							{if !empty($sim_title)}<div class="title"><span>{$sim_title}</span></div>{/if}
							{if !empty($sim.district.prepositional)}<div class="descr">{$sim.district.prepositional}</div>{/if}
							{if !empty($sim.properties.price_meter_from.value)}
								<div class="area">{$sim.properties.price_meter_from.value} <i>+</i><br><span>{$lang->get('тыс.рублей за м', 'ths rub. per m')}<sup>2</sup></span></div>
							{/if}
							{if !empty($url)}<a href="{$url}" class="btn m-sand m-vw">{$lang->get('Выбрать','Choose')}</a>{/if}
						</div>
					</div>
				{/foreach}
			</div>
		</div>
		<div class="swiper-button-prev{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
		<div class="swiper-button-next{if !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} a-hidden{/if}">{fetch file=$path . "arrow.svg"}</div>
	{/if}
	<div class="descr">{$lang->get('А почему бы не изучить предложения в готовых домах?', 'Why not check some apartments fo resale?')}</div>
	<a href="{$url_prefix}/resale/" class="btn m-light-magenta">{$lang->get('смотреть вторичную недвижимость','Search for resale apartments')}</a>
</div>
{*
{literal}
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Place",
  "name": "{/literal}{$title_arr[0]}{literal}",
  "url": "{/literal}{$page_url}{literal}",

  "address": {
    "@type": "PostalAddress",{/literal}
    {if !empty($item.address)}
		{literal}"streetAddress": "{/literal}{$item.address}{literal}",{/literal}
	{/if}
	{if !empty($item.district.title)}
		{literal}"addressLocality": "{/literal}{$item.district.title}{literal}"{/literal}
    {/if}
    {literal}
  },
  "photo": {
  "@type": "ImageObject",{/literal}
  {if !empty($seoImg)}
		{literal}"url": "{/literal}{$root_url . $seoImg->getCleanUrl()}{literal}"{/literal}
  {/if}
  {literal}
  }
}
</script>
{/literal}
*}
