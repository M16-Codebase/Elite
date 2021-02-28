{?$delim = ldelim . "!" . rdelim}
{?$title = $complex.title|replace:$delim:' '}

{if $request_segment.key == 'ru'}
    {?$pageTitle = $complex.properties[$infoblock_type].property.title . ' — ' . $title|strip_tags . ' | М16-Недвижимость'}
    {?$pageDescription = $complex.properties[$infoblock_type].property.title . ' — ' . $title|strip_tags . ' — вся самая необходимая информация об элитном жилом комплексе от М16-Недвижимость'}
{else}
    {?$pageTitle = $complex.properties[$infoblock_type].property.title . ' — ' . $title|strip_tags . ' | M16 Real Estate Agency'}
    {?$pageDescription = $complex.properties[$infoblock_type].property.title . ' — ' . $title|strip_tags . ' — the most necessary information about the elite residential complex at M16 Real Estate Agency'}
{/if}

{if $infoblock_type == 'public_space'}
    {if $request_segment.key == 'ru'}
		{?$header1 = 'Обустройство в ' . $title|strip_tags }
        {?$pageTitle = 'Обустройство в ЖК ' . $title|strip_tags . '. Квартиры в новостройках | М16-Недвижимость'}
        {?$pageDescription = 'Особенности инфраструктуры элитного ЖК ' . $title|strip_tags . '. Фото, описание и цены элитного жилого комплекса на сайте «М16-Недвижимость». Купить квартиру в ЖК ' . $title|strip_tags . ', СПб.'}
    {else}
        {?$header1 = 'Arrangement in ' . $title|strip_tags }
        {?$pageTitle = 'Arrangement in LCD ' . $title|strip_tags . '. Apartments in new buildings | M16 Real Estate Agency'}
        {?$pageDescription = 'Features of the infrastructure of the elite LCD ' . $title|strip_tags . '. Photo, description and prices of luxury residential complex on the site "M16-Real Estate".	Buy an apartment in ' . $title|strip_tags . ', St. Petersburg.'}
    {/if}
{/if}

{if $infoblock_type == 'engineer_solution'}
    {if $request_segment.key == 'ru'}
        {?$header1 = 'Инфраструктура в ' . $title|strip_tags }
        {?$pageTitle = 'Инфраструктура в ЖК ' . $title|strip_tags . '. Элитные квартиры | М16-Недвижимость'}
        {?$pageDescription = 'Инженерная инфраструктура в элитном ЖК ' . $title|strip_tags . '. Фото, подробная информация и цены элитного жилого комплекса на сайте «М16-Недвижимость». Поможем купить квартиру в элитном ЖК в СПб.'}
    {else}
        {?$header1 = 'Infrastructure in ' . $title|strip_tags }
        {?$pageTitle = 'Infrastructure in LCD ' . $title|strip_tags . '. Luxury apartments | M16 Real Estate Agency'}
        {?$pageDescription = 'Engineering infrastructure in the elite LCD ' . $title|strip_tags . '. Photo, detailed information and prices of luxury residential complex on the site "M16-Real Estate". We will help you to buy an apartment in an elite LCD in St. Petersburg.'}
    {/if}
{/if}

{if $infoblock_type == 'concept'}
    {if $request_segment.key == 'ru'}
        {?$header1 = 'Концепция ' . $title|strip_tags }
        {?$pageTitle = 'Концепция в ЖК ' . $title|strip_tags . '. Элитные ЖК от | М16-Недвижимость'}
        {?$pageDescription = 'Концепция и особенности элитного жилого комплекса ' . $title|strip_tags . '. Фото, инфраструктура, планировки и цены в ЖК. Продажа квартир в элитных жилых комплексах СПб по ценам от застройщиков.'}
    {else}
        {?$header1 = 'The concept ' . $title|strip_tags }
        {?$pageTitle = 'The concept in LCD ' . $title|strip_tags . '. Luxury LC from | M16 Real Estate Agency'}
        {?$pageDescription = 'The concept and features of the elite residential complex ' . $title|strip_tags . '. Photos, infrastructure, layouts and prices in the LCD. Sale of apartments in elite residential complexes of St. Petersburg at prices from developers.'}
    {/if}
{/if}

{if $infoblock_type == 'parking'}
    {if $request_segment.key == 'ru'}
        {?$header1 = 'Паркинг в ' . $title|strip_tags }
        {?$pageTitle = 'Паркинг в ЖК ' . $title|strip_tags . '. Квартиры в ЖК от | М16-Недвижимость'}
        {?$pageDescription = 'Паркинг в элитном ЖК ' . $title|strip_tags . ': парковочные места и особенности. Описание, планировки, фото и цены в жилом комплексе. В агентстве «М16-Недвижимость» вы можете купить квартиру в новых элитных ЖК СПб.'}
    {else}
        {?$header1 = 'Parking in ' . $title|strip_tags }
        {?$pageTitle = 'Parking in the LCD ' . $title|strip_tags . '. Apartments in the LCD from | M16 Real Estate Agency'}
        {?$pageDescription = 'Parking in the elite LCD ' . $title|strip_tags . ': parking spaces and features. Description, lay-out, photos and prices in a residential complex. In the agency "M16-Real Estate" you can buy an apartment in the new elite LCD SPb.'}
    {/if}
{/if}


{if $infoblock_type == 'materials'}
    {if $request_segment.key == 'ru'}
        {?$header1 = 'Архитектура ' . $title|strip_tags }
        {?$pageTitle = 'Архитектура и материалы ' . $title|strip_tags . '. Элитные квартиры в ЖК от | М16-Недвижимость'}
        {?$pageDescription = 'Архитектура элитного жилого комплекса ' . $title|strip_tags . ' в СПб. Фото, стилистика, использованные материалы и особенности архитектуры ЖК – на официальном сайте агентства «М16-Недвижимость». Продажа квартир в жилом комплексе ' . $title|strip_tags}
    {else}
        {?$header1 = 'Architecture ' . $title|strip_tags }
        {?$pageTitle = 'Architecture and materials ' . $title|strip_tags . '. Elite apartments in the LCD from | M16 Real Estate Agency'}
        {?$pageDescription = 'The architecture of the elite residential complex ' . $title|strip_tags . 'Photos, stylistics, materials used and features of the LCD architecture - on the official website of the agency "M16-Real Estate". Apartments for sale in a residential complex ' . $title|strip_tags}
    {/if}
{/if}



{include file='/components/main_menu.tpl' item=$complex}

{if !empty($header1)}
	<h1>{$header1}</h1>
{/if}


{if $infoblock_type == 'progress'}
	{if !empty($complex.gallery)}{?$cover = $complex.gallery->getCover()}{/if}
	<div class="top-bg" id="site-top">
		{if !empty($complex->getUrl())}<a href="{$complex->getUrl()}" class="back">{fetch file=$path . "arrow.svg"}</a>{/if}
		<div class='bg-img' style='background: url(/img/veil.png), url({!empty($cover) ? $cover->getUrl() : ''});background-size:cover;'></div>
		<div class="site-top">
			<h1 class="title" title="Ход строительства {$title}"><span>Ход строительства</span><br>{$title}</h1>
			<div class="btn m-sand">Выбрать на плане дома</div>
			{if !empty($complex.icon)}
				<div class="icon-block">
					{foreach from=$complex.icon item=icon}
						<div class="skew m-sand-skew">{$icon}</div>
					{/foreach}
				</div>
			{/if}
		</div>
	</div>
	{if !empty($complex[$infoblock_type])}
	<div class="progress-wrap post">
		{foreach from=$complex[$infoblock_type] item=it name=foo}
			<h2 class="title"><span>{$it.title}</span></h2>
			<div class="text m-borders">
				{$it.text|html}
			</div>
			<div class="gallery">
				{if !empty($it.gallery)}
					{?$gallery = $it.gallery->getImages()}
					{foreach from=$gallery item=img}
						<a rel="group_g" title="{$img.text}" href="{$img->getUrl()}" class="fancybox"><img src="{$img->getUrl(222,148,true)}" alt=""></a>
					{/foreach}
				{/if}
			</div>
		{/foreach}
	</div>
	{/if}
{else}
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
<div class="flat-wrap m-vw">
	<div class="swiper-container">
		<div class="swiper-wrapper">
			{foreach from=$complex[$infoblock_type] item=it name=foo}
				{if !empty($it.gallery)}{?$cover = $it.gallery->getCover()}{/if}
				<div class="swiper-slide">
					{if !empty($cover)}
					<div class="img-wrap" style="background-image: url({$cover->getUrl()});background-position:{if !empty($gravity)} {$gravity[$cover.gravity]}{/if};">
					</div>
					{/if}
					<div class='params post'>
						{if !empty($it.title)}<h2 class="title" title="{$it.title|truncate:40:""}"><span>{$it.title|truncate:40:""}</span></h2>{/if}
						{if !empty($it.title)}<h3 class="descr" title="{$it.annotation|truncate:90:""}">{$it.annotation|truncate:90:""}</h3>{/if}
						{if !empty($it.title)}<div class="text">{$it.text|html|truncate:170:""}</div>{/if}
						
						<a href="{$url_prefix}/real-estate/request/?id={$complex.id}" style="color: #fff;" class="btn m m-magenta-fill m-vw">Оставить заявку</a>
					</div>
				</div>
			{/foreach}
		</div>

		<div class="swiper-container-after">
		
			{foreach from=$complex[$infoblock_type] item=it name=foo}
				<div class='params post'>
					{if !empty($it.title)}<h2 class="title" title="{$it.title|truncate:40:""}"><span>{$it.title|truncate:40:""}</span></h2>{/if}
					{if !empty($it.title)}<h3 class="descr" title="{$it.annotation|truncate:90:""}">{$it.annotation|truncate:90:""}</h3>{/if}
					{if !empty($it.title)}<div class="text">{$it.text|html|truncate:170:""}</div>{/if}
					
					<a href="{$url_prefix}/real-estate/request/?id={$complex.id}" style="color: #fff;" class="btn m m-magenta-fill m-vw">Оставить заявку</a>
				</div>
				{break}
			{/foreach}
			{if !empty($smarty.foreach.foo.total) && $smarty.foreach.foo.total > 1}
			<div class="nav">
				<div class="pagin">1 / 1</div>
				<div class="swiper-pagination"></div>
				<div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
				<div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
			</div>
			{/if}
            <p style="" class="complex-back swiper-container-after-back">
				{$lang->get('Назад в ', 'Back to ')} <a href="{$complex->getUrl()}#paramsblock">ЖК {$title}</a>
			</p>
		</div>
	</div>
</div>
{?$black_footer = 1}
{/if}
