{if $request_segment.key == 'ru'}
  {?$pageTitle = 'Элитная недвижимость СПб | Продажа элитной недвижимости в агентстве «М16»'}
  {?$pageDescription = 'Агентство Вячеслава Малафеева – лучший выбор для тех, кто хочет купить элитную недвижимость в Санкт-Петербурге: предлагаем разные районы, цены и планировки. Бонус: автомобиль бизнес-класса с водителем, проживание в апартаментах, футбольные подарки.'}
{else}
  {?$pageTitle = 'Luxury real estate in St. Petersburg | Sale of luxury real estate in the agency "M16"'}
  {?$pageDescription = 'The agency of Vyacheslav Malafeev is the best choice for those who want to buy elite real estate in St. Petersburg: we offer different areas, prices and plans. Bonus: a business class car with a driver, accommodation in apartments, football gifts.'}
{/if}
<div class="main-front">

  <div class="main-front-title">

    <div class="table-block">
      <div class="cell-block">

      <div class="site-top">
        <div class="icon-block">
          <div class="skew m-sand-skew">
            {$lang->get('элитная недвижимость петербурга', 'Elite real estate in St. Petersburg')}
          </div>
        </div>
        <div class="title black-font" title="{$lang->get('Роскошь — удовольствие', 'Luxury is a pleasure')}">
          <span class="black-font">{$lang->get('Роскошь —', 'Luxury')}</span>
          <br>{$lang->get('удовольствие', 'is a pleasure')}
        </div>
      </div>

      </div>
    </div>

  </div>
{if !empty($page_banners)}
  <div class="gallery-tiles">

    <div class="gallery-big">
      <div class="swiper">
          <div class="swiper-container">
            <div class="swiper-wrapper">
				{foreach from=$page_banners item=slide name=slide_n}
					{if !empty($slide.image)}
						{if !empty($slide.destination)}<a {if $slide.link_type == 'external'}rel='nofollow' target='_blank'{/if} href='{if $slide.link_type == 'external'}http://{/if}{$slide.destination}'{else}<div{/if} class="swiper-slide" style="background-image: url({$slide.image->getUrl()});" ></{if !empty($slide.destination)}a{else}div{/if}>
					{/if}
			    {/foreach}
            </div>
          </div>
          <div class="swiper-button-prev{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
          <div class="swiper-button-next{if $smarty.foreach.slide_n.total < 2} a-hidden{/if}"><div class="arrow">{fetch file=$path . "arrow.svg"}</div></div>
      </div>
    </div>

  </div>
{/if}
</div>

<form method="GET" class="filter">
	<div>{include file='Modules/Catalog/RealEstate/filter_fields.tpl'}</div>
	<div class="main-filter-search_variants">

    <div class="wrap">
      <div class="row">
        <div class="col col1">

           <a href="{$url_prefix}/real-estate/" class="real-estate-link btn m-sand m-vw">
             {$lang->get('искать в строящихся домах', 'Search in new objects')}
           </a>

           <span class="main-filter-search_variants-title">
             — {$lang->get('Мощный инвестиционный потенциал', 'Powerful investment potential')}.
           </span>

        </div>
        <div class="col col2">

          <a href="{$url_prefix}/resale/" class="resale-link btn m-sand m-vw">
            {$lang->get('искать на вторичном рынке', 'Search for resale apartments')}
          </a>

          <span class="main-filter-search_variants-title">
            — {$lang->get('Достойная жизнь уже сейчас', 'Respectable living now')}.
          </span>

        </div>
      </div>
    </div>

  </div>
</form>

<div class="main-sc main-sc2 post" style="background: #f8f8f8;">

  <h1 class="title" title="{$lang->get('Купить элитную квартиру на ваших условиях', 'Buy luxury apartment on your special terms')}">
    <span>{$lang->get('Купить элитную квартиру', 'Buy luxury apartment')}</span>
    <br>
    {$lang->get('на ваших условиях', 'on your special terms')}
  </h1>

  <h3 class="descr" title="{$lang->get('Нужна помощь в сборе средств на покупку квартиры? Вы в другом городе? Нужно скорее продать старую квартиру?', 'Need help in collecting of funds to buy an apartment? Need help in collecting of funds to buy an apartment?')}">
    {$lang->get('Нужна помощь в сборе средств на покупку квартиры?', 'Need help in collecting of funds to buy an apartment?')} <br>
    {$lang->get('Вы в другом городе? Нужно скорее продать старую квартиру?', 'You are in another city? You should probably sell the old property?')}
  </h3>

</div>
<section class="section__top" style="background: #f8f8f8;">
<div class="main-sc">
  <div class="row">
      <div class="section__items">
          <div class="block__big-man"></div>
          <div class="block__items-list">
              <div class="block__item">
                  <div class="block__item-num">01</div>
                  <div class="block__item-title">
                      {$lang->get('Рекламные возможности', 'Advertising resources')}
                  </div>
                  <div class="block__item-text">
                      {$lang->get('Усилиями нашей команды профессионалов на продажу вашего объекта будут работать все ведущие маркет плейсы и эффективные digital и офф-лайновые инструменты по взаимодействую с потенциальными клиентами, начиная с контекстной и таргетовой рекламы и заканчивая рекламными щитами и радио.', 'Our team uses all the leading marketplaces and effective digital and offline tools for attracting potential customers. We operate with everything from contextual and targeted advertising to banners and radio.')}
                  </div>
              </div>
              <div class="block__item">
                  <div class="block__item-num">02</div>
                  <div class="block__item-title">
                      {$lang->get('Бесплатная оценка недвижимости', 'Real estate valuation')}
                  </div>
                  <div class="block__item-text">
                      {$lang->get('Наши специалисты быстро и бесплатно проведут первичную оценку стоимости лота, основываясь как на своем многолетнем опыте, так и на конкретных данных по рынку. А также сделают конечную корректировку после посещения объекта лично.', 'Our experts conduct a primary assessment of the lot value based on their long-term experience and current market data. The final cost will be determined after a personal visit to the property. The valuation is free of charge and takes a minimum of your time.')}
                  </div>
              </div>
              <div class="block__item">
                  <div class="block__item-num">03</div>
                  <div class="block__item-title">
                      {$lang->get('Своя база клиентов', 'Our client database')}
                  </div>
                  <div class="block__item-text">
                      {$lang->get('За более чем 7 лет продуктивной работы на рынке недвижимости мы собрали большую базу регулярных партнеров и постоянных клиентов, и наладили инструменты взаимодействия с ними. Информация о вашем лоте, после оценки и подписания договора, в обязательном порядке будет доведена до наших лояльных покупателей, которым он будет интересен.', 'For more than 7 years of productive work in the real estate market, we have gathered a large base of regular partners and customers. Immediately after evaluating the property and signing the contract, we will expedite a full presentation about the property to a pool of loyal interested clients.')}
                  </div>
              </div>
              <div class="block__item">
                  <div class="block__item-num">04</div>
                  <div class="block__item-title">
                      {$lang->get('Широкая партнерская сеть', 'Wide partner network')}
                  </div>
                  <div class="block__item-text">
                      {$lang->get('Независимо от того, хотите вы купить или продать недвижимость, на вас будет работать вся партнерская сеть компании М16 Недвижимость - более 500 застройщиков и риелторов', 'Regardless of whether you want to buy or sell real estate, there is our entire partner network at your service. M16 Group cooperates with more than 500 developers and realtors.')}
                  </div>
              </div>
              <div class="block__item">
                  <div class="block__item-num">05</div>
                  <div class="block__item-title">
                      {$lang->get('Финансовые гарантии продажи вашей квартиры', 'Financial guarantees of sale')}
                  </div>
                  <div class="block__item-text">
                      {$lang->get('Мы всегда ориентируемся на качество и высокий уровень оказания услуг, мы работаем честно и прозрачно и уверены в своих возможностях, а потому готовы подкреплять уверенность наших клиентов конкретными финансовыми обязательствами со своей стороны.', 'We always focus on the quality and high level of service. We work honestly and transparently, and we are confident in our capabilities. Therefore, we are happy to assure our clients and provide strict financial obligations on our part.')}
                  </div>
              </div>
          </div>
      </div>

  </div>
</div>
</section>

<div class="main-sc main-sc4">

  <a href="{$url_prefix}/service/" class="btn main-sc4-upper_btn m-light-magenta">
    {$lang->get('Все услуги', 'All services')}
  </a>

  <div class="bowtie-wrap row">
  <div class="w2">
  <div class="title">{$lang->get('Хотите <b>найти</b> себе<br>идеальную квартиру без усилий?', 'Trying <b>to find</b> yourself<br>the perfect apartment without effort?')|html}</div>
  <div class="descr">{$lang->get('Опытные специалисты в области недвижимости помогут Вам выбрать оптимальный вариант.', 'Qualified real estate professionals will help you to choose the best one.')}</div>
  <a href="{$url_prefix}/selection/" class="btn m-light-magenta m-vw">{$lang->get('Персональный подбор', 'Personal selection')}</a>
  </div>
  <div class="w2">
  <div class="title">{$lang->get('Хотите <b>продать</b> квартиру<br>премиум-класса выгодно и безопасно?', 'Want <b>to sell</b> luxury apartment safely and with real profit?')|html}</div>
  <div class="descr">{$lang->get('Мы оперативно найдем Вам покупателей и возьмем на себя юридическое сопровождение сделки.', 'We’ll find buyers in the shortest possible time and provide legal support of transactions.')}</div>
  <a href="{$url_prefix}/owner/" class="btn m-light-magenta m-vw">{$lang->get('Продать квартиру с М16', 'Sell apartment with M16')}</a>
  </div>
  <div class="bow_tie animated"></div>
  </div>

</div>

	{if !empty($complex_list)}
	<div class="main-sc main-sc5">
		<div class="about post">
			<h2 class="main m-vw" title="{$lang->get('Строящаяся элитная недвижимость в Санкт-Петербурге', 'Elite real estate under construction in St.Petersburg')}">
			  {$lang->get('Строящаяся элитная недвижимость в Санкт-Петербурге', 'Elite real estate under construction in St.Petersburg')}
			</h2>

			<div class="title">
			  <span>{$lang->get('Строящиеся объекты', 'New luxury buildings')}</span>
			</div>
			{if !empty($page_posts.real_estate.text)}
			<h3 class="descr" title="{$page_posts.real_estate.text}">
				{$page_posts.real_estate.text}
			</h3>
			{/if}
		</div>
	</div>

	<div class="main-sc main-sc6">

	  <div class="swiper">
		<div class="swiper-container">
		  <div class="swiper-wrapper">
			{foreach from=$complex_list item=complex name=complex_n}
				{if !empty($complex.gallery)}
					{?$cover = $complex.gallery->getCover()}
					<div class="swiper-slide">
						<a href="{$complex->getUrl()}" class="main-sc6-item">
							<div class="main-sc6-item-pic" {if !empty($cover)}style="background-image: url({$cover->getUrl(400,400)});"{/if} >
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

{if !empty($complex_count)}
	<div class="main-sc main-sc7">

	  <a href="{$url_prefix}/real-estate/" class="splited-block">
		<div class="splited-border m-left">
		  <span class="splited-inner">
			{$lang->get('СМОТРЕТЬ ВСЕ', 'Choose from')}
		  </span>
		</div>
		  <div class="splited-center">{$complex_count}</div>
		  <div class="splited-border m-right">
		  <span class="splited-inner">
			{if $request_segment.key == 'ru'}
				{$complex_count|plural_form:'новый объект':'новых объекта':'новых объектов':false}
			{else}
				new objects
			{/if}
		  </span>
		</div>
	  </a>

	</div>
{/if}


<div class="main-sc main-sc8">

  <div class="main-sc8-bg">
    <div class="main-sc8-bg-content"></div>
  </div>

  <div class="main-sc8-photo">
    <img src="/img/main-sc8-photo.png" alt="photo">
  </div>

  <div class="main-sc8-content">
    <div class="row">
      <div class="col col1">
        <div class="table-block">
          <div class="cell-block">


            <div class="main-sc8-quoute">
              {$lang->get('Траектория успеха —', 'Our way is the trajectory')} <br>
              {$lang->get('наш путь.  Мы не боимся', 'of success. We are not afraid of')} <br>
              {$lang->get('даже самых сложных сделок.', 'the most complex transactions,')} <br>
              {$lang->get('Cложности — это', 'because difficulties are')} <br>
              {$lang->get('единственный путь к', 'the only way')} <br>
              {$lang->get('развитию', 'to development')}.
            </div>


          </div>
        </div>
      </div>
      <div class="col col2">
        <div class="table-block">
          <div class="cell-block">


            <div class="main-sc8-desc">

              <div class="main-sc8-desc-line_1">
                {$lang->get('Авторский рейтинг', 'Personal rating')}
              </div>
              <div class="main-sc8-desc-line_2">
                <a href="{$url_prefix}/top16/" class="btn m-magenta-fill">
                  {$lang->get('Тор-16 от Малафеева', 'ТОР-16 BY MALAFEYEV')}
                </a>
              </div>

              <div class="main-sc8-desc-line_3">
                {$lang->get('вячеслав малафеев', 'Vyacheslav Malafeyev')}
              </div>
              <div class="main-sc8-desc-line_4">

                {$lang->get('Владелец агентства недвижимости M16', 'Owner of M16 Real Estate Agency')} <br>
                {$lang->get('Голкипер ФК «Зенит»', 'Goalkeeper in Zenit FC')}

              </div>
              <div class="main-sc8-desc-line_5">

                {fetch file=$path . "zenit.svg"}

              </div>



            </div>


          </div>
        </div>
      </div>
    </div>
  </div>

</div>
{if !empty($resale_list)}
<div class="main-sc main-sc9">

  <div class="about post">
    <h2 class="main m-vw" title="{$lang->get('Элитные квартиры в Санкт-Петербурге', 'Luxury apartments in St.Petersburg')}">
      {$lang->get('Элитные квартиры в Санкт-Петербурге', 'Luxury apartments in St.Petersburg')}
    </h2>

    <div class="title">
      <span>{$lang->get('Вторичная недвижимость', 'Resale apartments')}</span>
    </div>
	{if !empty($page_posts.resale.text)}
	<h3 class="descr" title="{$page_posts.resale.text}">
		{$page_posts.resale.text}
	</h3>
	{/if}
  </div>

</div>

<div class="main-sc main-sc10 map-cont">
	<div class="map main-sc10-map"  data-coords="[59.9491557,30.2958079]"  style="width:100%; height:500px;"></div>
	<div class="items-list a-hidden">
		{foreach from=$resale_list item=item name=name_n}
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
							{if !empty($floors)}{$floors[0]} {$lang->get('уровня', 'floors')}{/if}{if !empty($wc_number) && !empty($floors)}<span>•</span>{/if}
							{!empty($wc_number) ? $wc_number : ''}
							{if !empty($wc_number) && !empty($item.floor)}<span> • </span>{/if}{if !empty($item.floor)}{$lang->get('Этаж', 'Floor')} {$item.floor} {if $item.number_storeys}{$lang->get('из', 'of')} {$item.number_storeys}{/if}{/if}
						</div>
						{if !empty($item.properties.area_all.value)}
							<div class="area"><i>~</i>{$item.properties.area_all.value|round}<span>{$lang->get('м', 'm')}<sup>2</sup></span></div>
						{/if}
						{if !empty($item.overhang)}
							<div class="small-descr">
								{$lang->get('Есть ', 'With ')}
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
									<div class="price">{$item.properties.price.value} {$lang->get('млн руб.', 'mln rub')}</div>
									{if !empty($item.area_all)}
										<div class="small-descr">{($item.price/$item.properties.area_all.value*1000)|ceil} {$lang->get('тыс. руб. за м<sup>2</sup>', 'ths rub. per m<sup>2</sup>')|html}</div>
									{/if}
								{else}
									<div class="price m-noprice"><div>{$lang->get('по запросу', 'On request')}</div></div>
									<a href="{$url_prefix}/contacts/" class="small-descr">{$lang->get('Узнать цену', 'Find out price')}</a>
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



{if !empty($complex_count)}
<div class="main-sc main-sc11">

  <a href="{$url_prefix}/resale/" class="splited-block">
    <div class="splited-border m-left">
      <span class="splited-inner">
        {$lang->get('смотреть все', 'choose from')}
      </span>
    </div>
      <div class="splited-center">{$resale_count}</div>
      <div class="splited-border m-right">
      <span class="splited-inner">
		{if $request_segment.key == 'ru'}
			{$resale_count|plural_form:'квартиру':'квартиры':'квартир':false} на вторичном рынке
		{else}
			apartments for resale
		{/if}
      </span>
    </div>
  </a>

</div>
{/if}


{include file='/components/about.tpl' wife=1 items_list_flag=1 hide_main=1}
{include file='/components/main_seo_text.tpl' wife=1 items_list_flag=1 hide_main=1}