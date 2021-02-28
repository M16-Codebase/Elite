{?$pageTitle = (($request_segment.id==1)? 'Жилая недвижимость — Объекты — Управляющая компания Maris|CBRE' : 'Residential real estate — Objects — Management company Maris|CBRE')}
{?$pageDescription = (($request_segment.id==1)? 'Жилая недвижимость — Объекты — Управление и эксплуатация недвижимости. Компания Maris|CBRE' : 'Residential real estate — Objects — Management and operation of real estate. Company Maris|CBRE')}
{?$admin_link = '/catalog-item/?id=62'}
{*if !empty($countries)}
	{foreach from=$countries item=$country}
		<a href="{$country.url}">{$country.name}</a>
	{/foreach}
{/if*}
					
<div class="content-block">
	<div class="page-title">
		{include file="components/breadcrumb.tpl"}
		<h1>{if $request_segment.id==1}Жилая недвижимость{else}Residential real estate{/if}</h1>
	</div>
	<div class="justify aside-float-cont">
		<div class="aside-col object-aside-cont">
			<div class="aside-menu floating">
				<div class="aside-top">
					{if $request_segment.id==1}
						Города<br /> и страны
					{else}
						Cities<br /> and countries
					{/if}
				</div>
				<ul class="aside-menu-list">
					<li>
						<a href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/catalog/62/country-The+Great+Britain/city-London/">
							{if $request_segment.id==1}Лондон,<br /> Великобритания{else}London,<br /> United Kingdom{/if}
						</a>
					</li>
					<li class="no-actual">
						<a href="#">
							{if $request_segment.id==1}Санкт-Петербург,<br /> Россия — скоро{else}Saint Petersburg,<br /> Russia  — soon{/if}
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="countries-block main-col">
			<div class="countries-list">
				<ul class="countries-items-list">
					<li class="item country-1">
						<a href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/catalog/62/country-The+Great+Britain/city-London/">
							<div class="country-cont">
								<div class="cover">
									<img src="/img/design/country-1.jpg" alt="Лондон, Великобритания" />
								</div>
								<div class="country-title"><span class="title">{if $request_segment.id==1}Великобритания{else}United Kingdom{/if}</span></div>
								<div class="city-title"><span class="title">{if $request_segment.id==1}Лондон{else}London{/if}</span></div>
							</div>
						</a>
					</li>
					<li class="item country-2 coming-soon {if $request_segment.id!=1}soon-en{/if}">
						<a href="#">
							<div class="country-cont">
								<div class="cover">
									<img src="/img/design/country-2.jpg" alt="Санкт-Петербург, Россия" />
								</div>
								<div class="country-title"><span class="title">{if $request_segment.id==1}Россия{else}Russia{/if}</span></div>
								<div class="city-title"><span class="title">{if $request_segment.id==1}Санкт-Петербург{else}Saint Petersburg{/if}</span></div>
							</div>
						</a>
					</li>
				</ul>
			</div>
			<div class="share-block countries-share">
				<div class="share-cont justify">
					<div class="main col1">{if $request_segment.id==1}Поделиться{else}Share{/if}</div>
					<div class="share col3">
						<div class="b-share-wrap justify">
							<div class="share-title facebook"><div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook"></div> Facebook</div>
							<div class="share-title vkontakte"><div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte"></div> Vkontakte</div>
							<div class="share-title twitter"><div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter"></div> Twitter</div>
						</div>
					</div>
				</div>
				{*if !empty($post.annotation)}
					<div class="countries-lemma col3">
						<div class="text">
						{$post.annotation}
						</div>
					</div>
				{/if*}
			</div>
			{if !empty($post.text)}
				<div class="post-cont post-white">
					{if !empty($post.title)}
						<h1>{$post.title}</h1>
					{/if}
					{*if !empty($post.annotation)}
						<div class="annotation">{$post.annotation}</div>
					{/if*}
					<div class="text">
						{$post.text|html}
					</div>
				</div>
			{/if}
			{if !empty($actual_items)}
				<div class="similar">
					<div class="actual-catalog justify">
						<div class="actual-belt">
							<div class="actual-top">
								<div class="actual-title h1">{if $request_segment.id==1}АКТУАЛЬНЫЕ ПРЕДЛОЖЕНИЯ{else}CURRENT OFFERS{/if}</div>
							</div>
							{include file="components/catalog-belt.tpl" catalog_items=$actual_items countries_item=1}
						</div>
						<div class="actual-bottom"></div>
					</div>
				</div>
			{/if}
			
		</div>
	</div>
</div>		
<div class="catalog-bottom">
	<div class="green-line"></div>
	{include file="components/benefits.tpl"}
	{include file="components/news-block.tpl"}
	{include file="components/cbre-belt.tpl"}
</div>