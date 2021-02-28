{*{capture assign=objectsTitle}{if !empty($post.title)}{$post.title|html}{else}No title{/if}{/capture}
{?$pageTitle = $objectsTitle . (($request_segment.id==1)? '' : '')}
{?$pageDescription = $objectsTitle . (($request_segment.id==1)? '' : '')}*}
{?$admin_link = '/blog-admin/edit/?id=' . $post.id . '&s=' . $request_segment.id}

<div class="content-block news-page blog-page">
	<div class="page-title">
		{include file="components/breadcrumb.tpl" other_link=array(($request_segment.id==1)? 'Блог' : 'Blog' => $url_prefix . '/blog/')}
		<div class="h1">Корпоративный блог</div>
	</div>
	<div class="blog-block">
		<div class="blog-top justify">
			<div class="aside-col rss-link" data-href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/rss/blog.rss"><i></i> RSS-канал</div>
			<div class="main-col blog-search search-cont">
				<div class="search-inner">
					<form action="{if $request_segment.id!=1}/{$request_segment.key}{/if}/blog/" method="GET" class="search-form">
						<input type="text" name="tag" placeholder="Поиск по тегам" />
						<button class="btn btn-gray a-btn-green-light btn-search"><i></i>Искать</button>
						<div class="btn btn-gray btn-remove"><i></i></div>
					</form>
				</div>
			</div>
		</div>
		<div class="justify aside-float-cont">
			<div class="aside-col object-aside-cont">
				<div class="aside-menu floating">
					{*{?$author = $post.staff}*}
					{*{if !empty($author)}*}
						{*<div class="aside-top">*}
							{*{if $request_segment.id==1}*}
								{*Автор*}
							{*{else}*}
								{*Author*}
							{*{/if}*}
						{*</div>*}
						{*<div class="author">*}
							{*<div class="author-cover">*}
								{*{?$author_cover = $author['image']}*}
								{*{if !empty($author_cover)}*}
									{*<img src="{$author_cover->getUrl(60, 60, true)}" alt="{if !empty($author.name)}{$author.name}{/if}"/>*}
								{*{else}*}
									{*<img src="/img/design/user_no_photo.jpg" class="nocurator" alt="{if !empty($author.name)}{$author.name}{/if}"/>*}
								{*{/if}*}
								{*<span>*}
									{*{if !empty($author.name)}{$author.name}{/if}<br>*}
									{*{if !empty($author.surname)}{$author.surname}{/if}*}
								{*</span>*}
							{*</div>*}
							{*{if !empty($author.function)}*}
								{*<div class="small-descr">{$author.function}</div>*}
							{*{/if}*}
						{*</div>*}
					{*{/if}*}
					<div class="share-cont col1">
						<div class="share-cont-title">Поделиться</div>
						<div class="share">
							<div class="b-share-wrap">
								<div class="share-title facebook">Facebook <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="facebook"></div></div>
								<div class="share-title vkontakte">Vkontakte <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte"></div></div>
								<div class="share-title twitter">Twitter <div class="yashare-auto-init share-link" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="twitter"></div></div>
							</div>
						</div>
					</div>
					<div class="aside-link">
						См. также<br />
						<a href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/news/">Новости и пресс-релизы</a><br />
					</div>
				</div>
			</div>
			<div class="main-col">
				<div class="post-cont news">
					{?$comment_count = count($post.comment_ids) - 1}
					{if !empty($post)}
						<div class="post">
							{if !empty($post.title)}
								<h1>{$post.title}</h1>
							{/if}
							<div class="post-descr-cont">
								{if !empty($post.pub_date)}
									{?$post_date = $post.pub_date|strtotime}
									<div class="news-date">
										{if $request_segment.id==1}
											{$post.pub_timestamp|date_format_lang:'%e %B %Y, %H:%M', 'ru'}
										{else}
											{$post.pub_timestamp|date_format_lang:'%e %B %Y, %H:%M', 'en'}
										{/if}
									</div>
								{/if}
								{if $post.status != 'close'}
									<div class="comments-num">
										<i></i>
										{?$comment_count = count($post->getComments())}
										{if $comment_count == 0  && $post.status != 'close'}
											<a href="#.popup-add-comment" class="comment-add" data-toggle="popup" data-action="open">Комментировать</a>
										{else}
											<a href="#comment" class="scroll-to">
												{if $request_segment.id==1}
													{$comment_count|plural_form:'комментарий':'комментария':'комментариев'}
												{else}
													{$comment_count|plural_form:'comment':'comments':'comments'}
												{/if}
											</a>
										{/if}
									</div>
								{/if}
							</div>
							{if !empty($post.annotation)}
								<div class="news-annotation">{$post.annotation}</div>
							{/if}
							{if !empty($post.text)}
								<div class="news-text">{$post.text|html}</div>
							{/if}
							{if !empty($post.tag_list)}
								<div class="tags">
									<i></i><span>Тэги</span>
									<span class="tags-list">
										{?$first_tag = true}
										{foreach from=$post.tag_list item=$tag name=tag_item}
											<a href="{if $request_segment.id!=1}/{$request_segment.key}{/if}/blog/?tag={$tag}"><span>{if $first_tag != true}, {/if}</span>{$tag}</a>{?$first_tag = false}
										{/foreach}
									</span>
								</div>
							{/if}
						</div>
					{/if}
					{if $post.status == 'public'}
						<div class="comments-cont{if isset($smarty.get.add_comment)} active-comment{/if}" id="comment">
							{include file="Modules/Posts/Blog/commentList.tpl"}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>
<div class="catalog-bottom">
	<div class="green-line"></div>
</div>
	
{include file="Modules/Posts/Blog/popupAddComment.tpl"}



