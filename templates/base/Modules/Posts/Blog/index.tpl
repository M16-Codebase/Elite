{*{?$pageTitle = (($request_segment.id==1)? '' : '')}
{?$pageDescription = (($request_segment.id==1)? '' : '')}*}
{?$admin_link = '/blog-admin/?s=' . $request_segment.id}

<div class="content-block news-page blog-page">
	<div class="page-title">
		<h1>Корпоративный блог</h1>
	</div>
	<div class="blog-block">
		<div class="blog-top justify">
			<div class="main-col blog-search search-cont">
				<div class="search-inner{if empty($posts)} m-search{/if}">
					<form action="/blog/" method="GET" class="search-form">
						<input type="text" name="tag" placeholder="Поиск по тегам" />
						<button class="btn btn-gray a-btn-green-light btn-search"><i></i>Искать</button>
						<div class="btn btn-gray btn-remove"><i></i></div>
					</form>
				</div>
			</div>
		</div>
		{if !empty($posts)}
			<div class="blogs-list">
				<ul>
					{foreach from=$posts item=$blog name=blog_list}
						<li class="item ">
							<div class="justify">
								<div class="aside-col">
									{if !empty($blog.pub_timestamp)}
										<div class="date">
                                            {$blog.pub_timestamp|date_format_lang:'%e %B %Y', 'ru'}<br />
											В {$blog.pub_timestamp|date_format_lang:'%H:%M', 'ru'}
										</div>
									{/if}
									{*{?$author = $blog.staff}*}
									{*{if !empty($author)}*}
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
									{if $blog.status != 'close'}
										<div class="comments-num">
											<i></i>
											{?$comment_count = count($blog->getComments())}
											{if $comment_count == 0  && $blog.status != 'close'}
												<a href="{$blog->getUrl($request_segment.id)}/?add_comment" class="comment-add">Комментировать</a>
											{else}
												<a href="{$blog->getUrl($request_segment.id)}#comment">
                                                    {$comment_count|plural_form:'комментарий':'комментария':'комментариев'}
												</a>
											{/if}
										</div>
									{/if}
								</div>
								<div class="main-col">
									{?$cover = $blog.gallery->getCover()}
									{if !empty($cover)}
										<div class="blog-cover">
											<a href="{$blog->getUrl($request_segment.id)}">
												<img src="{$cover->getUrl(888,435,null)}" alt="{$blog.title}"/>
											 </a> 
										</div>
									{else}
										<div class="blog-desrc">
											<a href="{$blog->getUrl($request_segment.id)}" class="blog-title more-arrow">{if !empty($blog.title)}{$blog.title}{else}No title{/if}</a>
											{if !empty($blog.annotation)}
												<div class="blog-annotation">{$blog.annotation}</div>
											{/if}
										</div>
									{/if}
									{if !empty($blog.tag_list)}
										<div class="tags">
											<i></i><span>Тэги</span>
											<span class="tags-list">
												{?$first_tag = true}
												{foreach from=$blog.tag_list item=$tag name=tag_item}
													<a href="/blog/?tag={$tag}"><span>{if $first_tag != true}, {/if}</span>{$tag}</a>{?$first_tag = false}
												{/foreach}
											</span>
										</div>
									{/if}
								</div>
							</div>
						</li>
					{/foreach}
				</ul>
				{include file="components/paging.tpl"}
			</div>
		{else}
			<div class="empty-result">
				<div class="empty-result-cont">
					<div class="empty-title">
						Ничего не нашлось
					</div>
					<div class="empty-descr">
						{if $request_segment.id==1}
							К сожалению, по Вашему тегу мы не смогли ничего найти. Пожалуйста, попробуйте
							сформулировать запрос по-другому или найдите нужную запись в блоге.
						{else}
							Unfortunately, nothing was found with your tag. Please try new search.
						{/if}
					</div>
				</div>
			</div>
		{/if}
	</div>
</div>
<div class="catalog-bottom">
	<div class="green-line"></div>
</div>