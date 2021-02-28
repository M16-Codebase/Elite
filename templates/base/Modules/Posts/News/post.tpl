<div class="news-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			<div class="aside-catalog-select">
				{*include file="components/catalog-menu.tpl"*}
			</div>
			{*include file="components/company-menu.tpl"*}
		</div>
		{*include file="components/bonus-block.tpl"*}
	</aside>
	<div class="post-page main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{?$years = $post.timestamp|date_format_lang:'%Y', 'ru'}
				{include file="components/breadcrumb.tpl" other_link=array('Новости' => '/news/')}
				{if !empty($post)}
					<div class="info-block">
						{if !empty($post.title)}
							<h1>{$post.title}</h1>
						{/if}
						{if !empty($post.timestamp)}
							<div class="main">{$post.timestamp|date_format_lang:'%d %B %Y', 'ru'}</div>
						{/if}
						{if !empty($post.annotation)}
							<div class="annotation">{$post.annotation}</div>
						{/if}
						{if !empty($post.text)}
							{$post.text|html}
						{/if}
					</div>
				{/if}
			</div>
		</div>
		{if !empty($last_news)}
			<div class="carousel-items content-white-block">
				<div class="white-block-inner carousel">
					<div class="blue-arrows carousel-title-cont">
						<div class="carousel-title a-inline-cont">
							{if $last_news|@count > 4}
								<div class="car-prev">
									<div class="arrow arrow-left"></div>
								</div>
							{/if}
							<div class="title">Другие новости</div>
							{if $last_news|@count > 4}
								<div class="car-next">
									<div class="arrow arrow-right"></div>
								</div>
							{/if}
						</div>
					</div>
					<div class="car-wrap carousel-items-cont news-block">
						<ul class="types">
						{foreach from=$last_news item=$n name=news_list}
								<li class="catalog-item link-wrap post a-inline-cont">
									<div class="news-img news-item">
									{?$cover = $n.gallery->getCover()}
										{if !empty($cover)}
										   <a href="{$n->getUrl($request_segment.id)}">
											   <img src="{$cover->getUrl(166,166,90,null,true)}" alt="{$n.title}"/>
											</a> 
										{/if}
									</div>
									<div class="news-content">
										<a href="{$n->getUrl($request_segment.id)}" class="news-title">
											{$n.title}
										</a>
										<div class="news-info">
											{$n.annotation}
										</div>
										<div class="main">
											{$n.timestamp|date_format_lang:'%d %B %Y', 'ru'}
										</div>
									</div>
								</li>
						{/foreach}
						</ul>
					</div>
					{if $last_news|@count > 4}
						<div class="carousel-dots-cont">
							<ul class="carousel-dots car-pages a-inline-block a-inline-cont">
							</ul>
						</div>
					{/if}
				</div>
			</div>
		{/if}
	</div>
</div>