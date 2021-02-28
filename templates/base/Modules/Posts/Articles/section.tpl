<div class="mainpage buyers-page justify">
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{*{include file="components/buyers-breadcrumb.tpl"}*}
				<h1>111</h1>
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
								<div class="post-title"><a href="{$post->getUrl($request_segment.id)}" class="link-target">{$post.title}</a></div>
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