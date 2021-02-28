<div class="mainpage buyers-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			<div class="aside-catalog-select">
				{include file="components/catalog-menu.tpl"}
			</div>
			{include file="components/buyers-menu.tpl"}
		</div>
		{include file="components/bonus-block.tpl"}
	</aside>
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/buyers-breadcrumb.tpl"}
				<h1>Полезные советы</h1>
				{foreach from=$themes item=$theme}
					<ul class="post-list a-inline-cont">
						<li class="post-item link-wrap">
							<a href="/tip/section/?theme={$theme.id}" class="post-title link-target">{$theme.title}</a>
							<div class="main">{$theme.count|plural_form:'статья':'статьи':'статей'}</div>
						</li>
					</ul>
				{/foreach}	
			</div>
		</div>
	</div>
</div>
{include file="components/brands.tpl"}
{include file="components/news-block.tpl"}

{*foreach from=$themes item=$theme}
	{$theme.title}
	{$theme.count}
	<a href="/tip/section/?id={$theme.id}"></a>
{/foreach*}	