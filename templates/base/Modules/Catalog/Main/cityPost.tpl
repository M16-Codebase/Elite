{*
	/catalog/cityPost/?city={$enum_id}&post={$post_type}
	post_types = 'help','service','about'
*}
{if !empty($post)}{?$admin_link = '/city-post/edit/?id=' . $post.id}{/if}

<div class="content-block">
	<div class="page-title">
		{include file="components/breadcrumb.tpl"}
		<h1>
			{if $request_segment.id==1}Лондон{else}London{/if} —
			{if !empty($smarty.get.post)} 
				{if $smarty.get.post=='help'} {if $request_segment.id==1}Справочник районов{else}Reference areas{/if}
				{elseif $smarty.get.post=='service'} {if $request_segment.id==1}Услуги Maris в стране{else}Maris services in the country{/if}
				{elseif $smarty.get.post=='about'} {if $request_segment.id==1}Что нужно знать о покупке?{else}What you need to know about buying?{/if}
				{/if}
			{/if}
		</h1>
	</div>
	<div class="justify aside-float-cont">
		<div class="aside-col object-aside-cont">
			<div class="object-aside aside-menu floating">
				<div class="aside-top">
					{if $request_segment.id==1}
						Лондон,<br /> Великобритания
					{else}
						London,<br /> United Kingdom
					{/if}
				</div>
				<ul class="aside-menu-list">
					<li>
						<a href="{$current_city.url}">
							{if $request_segment.id==1}Каталог квартир{else}Catalogue of apartments{/if}
						</a>
					</li>
					<li{if !empty($smarty.get.post) && $smarty.get.post=='help'} class="m-current"{/if}>
						<a href="/{$request_segment.key}/catalog/cityPost/?city={$city_enum_id}&post=help">
							{if $request_segment.id==1}Справочник районов{else}Reference areas{/if}
						</a>
					</li>
					<li{if !empty($smarty.get.post) && $smarty.get.post=='service'} class="m-current"{/if}>
						<a href="/{$request_segment.key}/catalog/cityPost/?city={$city_enum_id}&post=service">
							{if $request_segment.id==1}Услуги Maris в стране{else}Maris services in the country{/if}
						</a>
					</li>
					<li{if !empty($smarty.get.post) && $smarty.get.post=='about'} class="m-current"{/if}>
						<a href="/{$request_segment.key}/catalog/cityPost/?city={$city_enum_id}&post=about">
							{if $request_segment.id==1}Что нужно знать о покупке?{else}What you need to know about buying?{/if}
						</a>
					</li>
				</ul>
				{include file="Modules/Catalog/Main/itemsFilter.tpl"}
				<div class="object-map">
					<div class="h3">{if $request_segment.id==1}Карта города{else}City Map{/if}</div>
					<div class="map-cover">
						<a href="/{$request_segment.key}{$current_city.url}?map=yes" class="tab-title group-map"><i></i>{if $request_segment.id==1}Лондон{else}London{/if}</a>
					</div>
					<div class="small-descr">{if $request_segment.id==1}Посмотрите город и наши предложения в нем{else}Look at our city and it offers{/if}</div>
				</div>
			</div>
		</div>			
		<div class="main-col">
			{if !empty($smarty.get.post) && $smarty.get.post=='help'}
				<div class="post-cover-cont">
					<div class="post-cover"><img src="/img/post_img.jpg" /></div>
					<div class="post-title">
						<span class="title">
							{if $request_segment.id==1}
								Где в центре Лондона лучшие рестораны? <br />
								Где можно жить, чтобы быть рядом с университетами?
							{else}
								Where in the heart of London's best restaurants? <br /> 
								Where can I live to be close to universities?
							{/if}
						</span>
					</div>
				</div>
			{else}
				<div class="top-cont"></div>
			{/if}
			<div class="share-block justify">
				<div class="share-cont col1">
					<div class="share">
						<div class="b-share-wrap">
							<div class="share-title facebook">Facebook <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook"></div></div>
							<div class="share-title vkontakte">Vkontakte <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte"></div></div>
							<div class="share-title twitter">Twitter <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter"></div></div>
						</div>
					</div>
				</div>
				{if !empty($post.annotation)}
					<div class="countries-lemma col3">
						<div class="text">
						{$post.annotation}
						</div>
					</div>
				{/if}
			</div>
			{if !empty($post)}
				<div class="post-cont post-white{if !empty($smarty.get.post) && $smarty.get.post=='help'} areas-descr{/if}">
					<div class="text">{$post.text|html}</div>
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