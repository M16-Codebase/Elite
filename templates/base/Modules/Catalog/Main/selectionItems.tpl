


{?$pageTitle = $current_type.title . ' для активного и экстримального отдыха — Apex Sport'}
{?$pageDescription = $current_type.title . ' для активного отдыха и экстримального спорта — Apex Sport: одежда и экипировка'}
{if !empty($single_manuf)}
	{?$pageTitle = $current_type.title . ' марки ' . $single_manuf . ' для активного и экстримального отдыха — Apex Sport'}
	{?$pageDescription = $current_type.title . ' марки ' . $single_manuf . ' для активного отдыха и экстримального спорта — Apex Sport: одежда и экипировка'}
{elseif !empty($single_using)}
	{?$pageTitle = $current_type.title . $single_using_title . ' — Apex Sport'}
	{?$pageDescription = $current_type.title . $single_using_title . ' — Apex Sport: одежда и экипировка'}
{/if}	

<div class="page-title">
	<div class="page-center">
		{*include file="components/breadcrumbs.tpl"*}
		<nav class="breadcrumbs" itemprop="breadcrumb">
			<ul>
				<li class="a-inline-block"><a href="/">Apex Sport</a></li>
				<li class="a-inline-block"><a href="/catalog/">Каталог</a></li>
				<li class="a-inline-block"><a href="/catalog/{$current_type.id}">{$current_type.title}</a></li>
			</ul>
		</nav>
        <div class="my_types_title">{$current_type.title}</div>
	</div>
</div>
		
<div class="main-content">
	<div class="page-center clearbox">
		<aside class="page-aside a-left">
			<div class="content-header">
				<h3>Подберите по вкусу</h3>
			</div>
			<div class="aside-content">
                {if !empty($type_articles)}					
					<div class="aside-links">
						<ul class="news-list">
							{foreach from=$type_articles item=art name="type_articles"}
								{if iteration <= 2}
									<li class=news-item>
										{?$cover = $art.gallery->getCover()}
										{if !empty($cover)}
											<a href="{$art.url}" class="cover">
												<img src="{$cover->getUrl(187, 150, 90, true)}" alt="{$art.title}" />
											</a>
										{/if}
										<a href="{$art.url}" class="title">{$art.title}</a>
									</li>
								{else}
									<li><a href="{$art.url}">{$art.title}</a></li>
								{/if}
								{if iteration == 2 && !last}
									</ul>
									<div class="aside-links-descr articles-title">Еще по теме</div>
									<ul class="articles-list">
								{/if}	
							{/foreach}	
						</ul>
					</div>
				{/if}
				<div class="bottom-float-line"></div>
			</div>
		</aside>
		
		<section class="catalog-page a-left">			
			
			<div class="items-list-cont">
				{include file="Modules/Catalog/Main/itemsList.tpl"}
			</div>
			
			{if empty($search_params) && !empty($current_type.post)}
				<div class="bordered-block edited-text type-descr">
                    <H1>{$current_type.post.title}</H1>
					{if !empty($current_type.annotation)}
						<p class="main">{$current_type.annotation}</p>
					{/if}
					{$current_type.post.text|html}
				</div>
			{/if}
			
		</section>
	</div>
</div>