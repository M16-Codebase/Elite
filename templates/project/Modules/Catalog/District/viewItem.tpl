{*<h1>{$item.title}</h1>

<p>{$item.post.annotation}</p>

<div>{$item.post.text|html}</div>*}
{?$delim = ' '}
{?$title_arr = $delim|explode:$item.title}
{?$title = $item.title|replace:$delim:' '}
{if $request_segment.key == 'ru'}
	{?$pageTitle = $title . ' — Гид по районам Санкт-Петербурга | М16-Недвижимость'}
	{?$pageDescription = $title . ' — описание элитного района Санкт-Петербурга: атмосфера и преимущества'}
{else}
	{?$pageTitle = $title . ' — St.Petersburg districts guidebook | M16 Real Estate Agency'}
	{?$pageDescription = $title . ' — description of the elite district of St.Petersburg: the atmosphere and benefits'}
{/if}
<div class="top-bg m-white">
	<a href="{$url_prefix}/district/" class="back">{fetch file=$path . "arrow.svg"} {$lang->get('Все районы', 'All areas')}</a>
	<div class="site-top">
		<h1 class="title" {if !empty($title)}title="{$title}"{/if}>{if !empty($title_arr[0])}<span>{$title_arr[0]}</span><br>{/if} {if !empty($title_arr[1])}{$title_arr[1]}{/if}</h1>
		<div class="main">{$lang->get('в Санкт-Петербурге', 'of St.Petersburg')}</div>
	</div>
</div>
<div class="swipe-wrap m-see-more">
	{if !empty($resale) || !empty($real_estate)}
		<div class="swiper-container">
			<div class="w4 swiper-wrapper">
				{if !empty($real_estate)}
					{foreach from=$real_estate item=sim name=sim_n}
						{?$cover = !empty($sim.gallery) ? $sim.gallery->getCover() : null}
						{?$delim = ldelim . "!" . rdelim}
						{?$sim_title = $sim.title|replace:$delim:' '}
						{?$url = !empty($sim->getUrl()) ? $sim->getUrl() : null}
						<div class="swiper-slide m-vw flat-wrap{if empty($resale)} m-center{elseif !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} m-margin{/if}">
							<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url('/img/veil.png'), url({$cover->getUrl(940, 650)}){if !empty($gravity)} {$gravity[$cover.gravity]}{/if}; background-size:cover;">
							</a>
							<div class='params'>
								<div class="main m-vw">{$lang->get('Актуальные предложения', 'Actual offers')}</div>
								<div class="title"><span>{$lang->get('Строящаяся недвижимость', 'New luxury buildings')}</span></div>
								{if !empty($item.prepositional)}<div class="descr">{$item.prepositional}</div>{/if}
								{if !empty($item.properties.price_primary.value)}
									<div class="area">{$item.properties.price_primary.value} <i>+</i><br><span>{$lang->get('тыс.рублей за м', 'ths rub. per m')}<sup>2</sup></span></div>
								{/if}
								<a href="{$url_prefix}/real-estate/?district[]={$item.id}" class="btn m-sand m-vw">{$lang->get('Выбрать', 'Choose')}</a>
							</div>
						</div>
						{break}
					{/foreach}
				{/if}
				
				{if !empty($resale)}
					{foreach from=$resale item=sim name=sim_n}
						{?$cover = !empty($sim.gallery) ? $sim.gallery->getCover() : null}
						{?$delim = ldelim . "!" . rdelim}
						{?$sim_title = $sim.title|replace:$delim:' '}
						{?$url = !empty($sim->getUrl()) ? $sim->getUrl() : null}
						<div class="swiper-slide m-vw flat-wrap{if empty($real_estate)} m-center{elseif !empty($smarty.foreach.sim_n.total) && $smarty.foreach.sim_n.total < 3} m-margin{/if}">
							<a href="{if !empty($url)}{$url}{/if}" class='cover' style="background: url('/img/veil.png'), url({$cover->getUrl(940, 650, true)}) {if !empty($gravity)} {$gravity[$cover.gravity]}{/if}; background-size:cover;">
							</a>
							<div class='params'>
								<div class="main m-vw">{$lang->get('Актуальные предложения', 'Actual offers')}</div>
								<div class="title"><span>{$lang->get('Вторичная недвижимость', 'Resale apartments')}</span></div>
								{if !empty($item.prepositional)}<div class="descr">{$item.prepositional}</div>{/if}
								{if !empty($item.properties.price_resale.value)}
									<div class="area">{$item.properties.price_resale.value} <i>+</i><br><span>{$lang->get('млн рублей', 'mln rubles')}</span></div>
								{/if}
								<a href="{$url_prefix}/resale/" class="btn m-sand m-vw">{$lang->get('Выбрать', 'Choose')}</a>
							</div>
						</div>
						{break}
					{/foreach}
				{/if}
			</div>
		</div>
	{/if}
	<div class="descr">{$lang->get('Выбираете квартиру', 'Looking for apartment')} {$item.prepositional}? {$lang->get('Мы поможем с выбором', 'We can help you')}</div>
	<a href="{$url_prefix}/contacts/#form" class="btn m-light-magenta">{$lang->get('Получить консультацию лично', 'Get your personal advice')}</a>
</div>
{if !empty($item.post)}
	<div class="post">
		<h2 class="descr"><span>{$item.post.annotation}</span></h2>
		<div class="text m-borders">
			{$item.post.text|html}
		</div>
	</div>
{/if}

{if !empty($real_estate)}
<div class="main-sc main-sc5">
  <div class="about post">
  <h2 class="main m-vw" title="{$lang->get('Актуальные предложения на строющуюся недвижимость', 'Elite real estate under construction in St.Petersburg')}">
	  {$lang->get('Актуальные предложения на строющуюся недвижимость', 'Elite real estate under construction in St.Petersburg')}
  </h2>

  <div class="title">
    <span>{$lang->get('Строящиеся объекты', 'New luxury objects')}</span>
  </div>
	{if !empty($item.prepositional)}
	  <h3 class="descr" title="{$item.prepositional}">
		{$item.prepositional}
	  </h3>
	</div>
	{/if}
</div>
  
<div class="main-sc main-sc6">

  <div class="swiper">
    <div class="swiper-container">
      <div class="swiper-wrapper">
		{foreach from=$real_estate item=complex name=complex_n}
			{if !empty($complex.gallery)}
				{?$cover = $complex.gallery->getCover()}
				<div class="swiper-slide">
					<a href="{$complex->getUrl()}" class="main-sc6-item">
						<div class="main-sc6-item-pic" {if !empty($cover)}style="background-image: url({$cover->getUrl()});"{/if} >
							{if !empty($complex.logo)}
							<div class="main-sc6-item-logo">
								<img src="{$complex.logo->getUrl()}" alt="">
							</div>
							{/if}
						</div>
						{if !empty($complex.title)}
							{?$delim = ldelim . "!" . rdelim}
							{?$title = $complex.title|replace:$delim:' '}
							<div class="main-sc6-item-desc">
								<div class="main-sc6-item-title">
									{$title}
								</div>
								{if !empty($complex.district.prepositional)}
								<div class="main-sc6-item-caption">
									{$complex.district.prepositional}
								</div>
								{/if}
							</div>
						{/if}
					</a>
				</div>
			{/if}
		{/foreach}
      </div>
    </div>
    <div class="swiper-button-prev">{fetch file=$path . "arrow.svg"}</div>
    <div class="swiper-button-next">{fetch file=$path . "arrow.svg"}</div>
  </div>

</div>
{/if}

{if !empty($resale)}
<div class="main-sc main-sc9">

  <div class="about post">
    <h2 class="main m-vw" title="{$lang->get('Актуальные предложения на элитные квартиры ', 'Luxury apartments in St.Petersburg')}">
		{$lang->get('Актуальные предложения на элитные квартиры ', 'Luxury apartments in St.Petersburg')}
    </h2>

    <div class="title">
      <span>{$lang->get('Вторичная недвижимость', 'Resale apartments')}</span>
    </div>

    <h3 class="descr" title="{$item.prepositional}">
     {$item.prepositional}
    </h3>
  </div>

</div>

<div class="main-sc main-sc10 map-cont">
	<div class="map main-sc10-map"  data-coords="[59.9491557,30.2958079]"  style="width:100%; height:650px;"></div>
	<div class="items-list a-hidden">
		{foreach from=$resale item=item name=name_n}
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
					<div class="params">
						<div class="main">{$item.address}</div>
						<div class="title"><span>{$lang->get($item.bed_number|plural_form:'спальня':'спальни':'спален', $item.bed_number|plural_form:'bedrooms':'bedroom':'bedrooms')}</span></div>
						{?(!empty($item.properties.floors.real_value) && $item.properties.floors.value_key != 'one') ? $floors = ' '|explode:$item.properties.floors.real_value : $floors = NULL}
						{if !empty($item.wc_number)}{?$wc_number = $lang->get($item.wc_number|plural_form:'санузел':'санузла':'санузлов', $item.wc_number|plural_form:'bathroom':'bathrooms':'bathrooms')}{/if}
						<div class="descr">
							{if !empty($floors)}{$floors[0]} {$lang->get('уровня', 'levels')}{/if}{if !empty($wc_number) && !empty($floors)}<span>•</span>{/if}
							{!empty($wc_number) ? $wc_number : ''}
							{if !empty($wc_number) && !empty($item.floor)}<span>•</span>{/if}{if !empty($item.floor)}{$lang->get('Этаж', 'Floor')} {$item.floor} {if $item.number_storeys}{$lang->get('из', 'of')} {$item.number_storeys}{/if}{/if}
						</div>
						{if !empty($item.properties.area_all.value)}
							<div class="area"><i>~</i>{$item.properties.area_all.value|round} <span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
						{/if}
						{if !empty($item.overhang)}
							<div class="small-descr">
								{$lang->get('Есть', 'With')}
								{foreach from=$item.overhang item=overhang name=overhang_n}
									{$overhang}{if !$smarty.foreach.overhang_n.last} + {/if}
								{/foreach}
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
									<div class="price m-noprice"><div>{$lang->get('по запросу', 'by request')}</div></div>
									<a href="{$url_prefix}/resale/request/?id={$complex.id}" class="small-descr">{$lang->get('Узнать цену', 'Find out price')}</a>
								{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		{/foreach}
	</div>
</div>
{/if}
<div class="resale-wrap">
	<div class="resale m-padding">
		<div class="wrap">
			<div class="descr">{$lang->get('Не знаете какой район выбрать? Мы поможем с выбором', 'Hard to decide what district to choose? We can help you')}</div>
			<a href="{$url_prefix}/contacts/#form" class="btn m-dark-magenta">{$lang->get('Получить консультацию лично', 'Get your personal advice')}</a>
		</div>
	</div>
	<div class="resale">
		<div class="wrap">
			<div class="descr">{$lang->get('Мы готовы взять труд по подбору идеальной квартиры на себя', 'We are willing to take the trouble of choosing the perfect apartament ')}</div>
			<div class="buttons">
				<a href="{$url_prefix}/selection/" class="btn m-dark-magenta">{$lang->get('Оставить заявку', 'Send your request')}</a>
			</div>
		</div>
	</div>
</div>