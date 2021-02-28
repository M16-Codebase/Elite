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
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/breadcrumb.tpl" other_link=array('Новости' => '/news/')}
				<h1>Новости</h1>
				{if !empty($years)}
					<div class="news-time">
						{foreach from=$years item=year}
							<a {if $req_year == $year}class="m-current"{/if} href="/news/{$year}/">{$year}</a></a>
						{/foreach}
					</div>
				{/if}	
				<div class="news-block">
					<ul class="info-items justify">
						{foreach from=$posts item=$n name=news_list}
							{if iteration is even && !first}
									</ul>
									<ul class="info-items justify{if iteration is even && iteration > 3} more-item a-hidden{/if}">
								{/if}
								<li class="col2 a-inline-cont">
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
                                        {if !empty($search_matches[$n.id])}
                                            <div class="news-info">
                                                {$search_matches[$n.id]|html}
                                            </div>
                                        {elseif !empty($n.annotation)}
											<div class="news-info">
												{$n.annotation}
											</div>
										{/if}
										{if !empty($n.timestamp)}
											<div class="main">
												{$n.timestamp|date_format_lang:'%d %B %Y', 'ru'}
											</div>
										{/if}
									</div>
								</li>
						{/foreach}
					</ul>
				</div>
			</div>
			{if count($posts) > 2}
				<div class="grey-block-inner">	
					<div class="more-btn">
						<a class="btn btn-grey-blue a-inline-block" href="#" data-alt-text="Скрыть новости">Показать еще новости</a>
					</div>
				</div>
			{/if}	
		</div>
	</div>
</div>