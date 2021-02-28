{if $request_segment.key == 'ru'}
	{?$pageTitle = 'ТОП-16 от В. Малафеева | М16-Недвижимость'}
	{?$pageDescription = 'ТОП-16 лучших элитных квартир для самых требовательных клиентов, разработанный самим Вячеславом Малафеевым'}
{else}
	{?$pageTitle = 'ТОП-16 by Vyacheslav Malafeyev | M16 Real Estate Agency'}
	{?$pageDescription = 'TOP 16 of the best luxury apartments for the most demanding of clients drawn up by Vyacheslav Malafeyev'}
{/if}
<div class="top-bg m-white">
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Топ-16 предложений', 'Top-16 list of residential offers')}">{$lang->get('<span>Топ-16</span><br>предложений', '<span>Top-16</span><br>list of offers')|html}</h1>
		<div class="main">{$lang->get('от Вячеслава Малафеева', 'by Vyacheslav Malafeyev')}</div>
	</div>
	<div class="top-links a-inline-cont a-center">
		<a href="#real-estate" class="scroller">{$lang->get('Строящаяся элитная недвижимость', 'Elite real estate under construction')}</a>
		<span class="slash"></span>
		<a href="#resale" class="scroller">{$lang->get('Вторичная элитная недвижимость', 'Luxury apartments fo resale')}</a>
	</div>
</div>

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
						{$lang->get('Предлагаю Вам мой<br>авторский рейтинг<br>объектов элитной<br>недвижимости<br>из каталога М16.<br>Я лично раз в месяц<br>составляю его для Вас.', 'Let me present you<br>my rating of elite<br>real estate from<br>M16 catalog.<br>I personally draw up it<br>for you once a month.')|html}
					</div>
				</div>
			</div>
		</div>
		<div class="col col2">
			<div class="table-block">
				<div class="cell-block">
					<div class="main-sc8-desc">
						<div class="logo"></div>
						<div class="main-sc8-desc-line_3">
							{$lang->get('Вячеслав Малафеев', 'Vyacheslav Malafeyev')}
						</div>
						<div class="main-sc8-desc-line_4">
							{$lang->get('Владелец агентства недвижимости M16', 'Owner of M16 Real Estate Agency')}<br>
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


<div class="main-sc main-sc5" id="real-estate">
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
	<div class="items-belt a-justify">
		{?$topitems_count = 0}
		{if !empty($t16_real_estate)}
			{foreach from=$t16_real_estate item=complex name=complex_n}
				{if $topitems_count >= 8}{break}{/if}
				{if !empty($complex.gallery)}
					{?$topitems_count++}
					{?$cover = $complex.gallery->getCover()}
					<a href="{$complex->getUrl()}" class="main-sc6-item">
						<div class="counter"><div class="skew m-sand-skew">{$topitems_count}</div></div>
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
				{/if}
			{/foreach}
		{/if}
		{if $topitems_count < 8}
			{section loop=8 start=$topitems_count name=topitems}
				{?$topitems_count++}
					<div class="main-sc6-item m-empty">
						<div class="counter"><div class="skew m-sand-skew">{$topitems_count}</div></div>
						<div class="empty-content">
							<div class="main-sc6-item-title">{$lang->get('МЕСТО ПОКА<br>НЕ ЗАНЯТО', 'THIS PLACE<br>IS EMPTY')|html}</div>
							<div class="main-sc6-item-caption">{$lang->get('Кандидаты<br>рассматриваются', 'Applicants<br>are under discussion')|html}</div>
						</div>
					</div>
			{/section}
		{/if}
	</div>
</div>
	
	
<div class="main-sc main-sc5" id="resale">
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
<div class="main-sc main-sc6">
	<div class="items-belt a-justify">
		{?$topitems_count = 0}
		{if !empty($t16_resale)}
			{foreach from=$t16_resale item=flat name=flat_n}
				{if $topitems_count >= 8}{break}{/if}
				{if !empty($flat.gallery)}
					{?$topitems_count++}
					{?$cover = $flat.gallery->getCover()}
					{if $topitems_count == 5}</div><div class="items-belt a-justify">{/if}
					<a href="{$flat->getUrl()}" class="main-sc6-item">
						<div class="counter"><div class="skew m-sand-skew">{$topitems_count}</div></div>
						<div class="main-sc6-item-pic" {if !empty($cover)}style="background-image: url({$cover->getUrl()});"{/if} ></div>
						{if !empty($flat.title)}
							{?$delim = ldelim . "!" . rdelim}
							{?$title = $flat.title|replace:$delim:' '}
							<div class="main-sc6-item-desc">
								<div class="main-sc6-item-title">
									{$title}
								</div>
								{if !empty($flat.district.prepositional)}
									<div class="main-sc6-item-caption">
										{$flat.district.prepositional}
									</div>
								{/if}
							</div>
						{/if}
					</a>
				{/if}
			{/foreach}
		{/if}
		{if $topitems_count < 8}
			{section loop=8 start=$topitems_count name=topitems}
				{?$topitems_count++}
				{if $topitems_count == 5}</div><div class="items-belt a-justify">{/if}
					<div class="main-sc6-item m-empty">
						<div class="counter"><div class="skew m-sand-skew">{$topitems_count}</div></div>
						<div class="empty-content">
							<div class="main-sc6-item-title">{$lang->get('МЕСТО ПОКА<br>НЕ ЗАНЯТО', 'THIS PLACE<br>IS EMPTY')|html}</div>
							<div class="main-sc6-item-caption">{$lang->get('Кандидаты<br>рассматриваются', 'Applicants<br>are under discussion')|html}</div>
						</div>
					</div>
			{/section}
		{/if}
	</div>
</div>

<div class="top-100-logo-16">
	<a href="/top-100/">
	<img src="/templates/project/img/top-100-logo-16.png" alt="" title="">
	</a>
</div>

	
	
{if !empty($page_posts.main_post) && $page_posts.main_post.status == 'close'}
    <h4>{$page_posts.main_post.title}</h4>
    <p>{$page_posts.main_post.annotation}</p>
    <div>{$page_posts.main_post.text|html}</div>
{/if}