<div class="mainpage buyers-page justify">
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{*{include file="components/buyers-breadcrumb.tpl"}*}
				<h1>Статьи</h1>
				{foreach from=$themes item=$theme}
					<ul class="post-list a-inline-cont">
						<li class="post-item link-wrap">
							<a href="/article/section/?theme={$theme.id}" class="post-title link-target">{$theme.title}</a>
							<div class="main">{$theme.count|plural_form:'статья':'статьи':'статей'}</div>
						</li>
					</ul>
				{/foreach}	
			</div>
		</div>
	</div>
</div>