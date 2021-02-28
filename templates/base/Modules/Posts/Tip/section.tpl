<div class="mainpage buyers-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			<div class="aside-catalog-select">
				{include file="components/catalog-menu.tpl"}
			</div>
			{include file="components/buyers-menu.tpl"}
		</div>
		{include file="components/bonus-block.tpl"}
		<div class="white-block-inner benefits-cont content-white-block">
			{include file="components/benefits.tpl"}
		</div>
	</aside>
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/buyers-breadcrumb.tpl"}
				<h1>{$current_theme.title}</h1>
			</div>
			<div class="grey-block-inner">
				<ul class="post-list">
					{foreach from=$posts item=$post name=post_list}
						<li class="post-block content-white-block justify link-wrap{if iteration > 5} more-item a-hidden{/if}">
							{?$cover = $post.gallery->getCover()}
							{if !empty($cover)}
								<div class="col1 post-image">
									<img src="{$cover->getUrl()}" alt="{$post.title}"/>
								</div>
							{/if}
							<div class="col3 post-right">
								<div class="post-title"><a href="{$post.url}" class="link-target">{$post.title}</a></div>
								{if !empty($post.annotation)}
									<div class="post-annotation">{$post.annotation}</div>
								{/if}
							</div>
						</li>
					{/foreach}
				</ul>
				{if count($posts) > 5}
					<div class="more-btn">
						<a class="btn btn-grey-blue a-inline-block" href="#" data-alt-text="Скрыть статьи">Показать еще статьи</a>
					</div>
				{/if}
			</div>
		</div>
	</div>
</div>
{include file="components/brands.tpl"}
{include file="components/news-block.tpl"}


{*foreach from=$posts item=$post}
	{$post.title}
{/foreach*}