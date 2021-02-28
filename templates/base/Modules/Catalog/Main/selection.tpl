{?$pageTitle = 'Каталог товаров для активного и экстримального отдыха — Apex Sport'}
{?$pageDescription = 'Каталог товаров для активного отдыха и экстримального спорта — Apex Sport: одежда и экипировка'}

<div class="page-title">
	<div class="page-center">
		{*include file="components/breadcrumbs.tpl"*}
		<nav class="breadcrumbs" itemprop="breadcrumb">
			<ul>
				<li class="a-inline-block"><a href="/">Apex Sport</a></li>
			</ul>
		</nav>
		<h1>{$selection_page.title}</h1>
	</div>
</div>

<div class="main-content">
	<div class="page-center clearbox">
		<div class="brand-page">
			{if !empty($types)}
				<div class="types-list catalog-page">
					<ul class="catalog-list clearbox">
						{foreach from=$types item=type}
							{?$count = $count_by_type[$type.id]}
							<li class="catalog-item no-touch-hover">
								<div class="cover-cont">
									<div class="cover">
                                        {*<img src="{$type.cover->getUrl(180,220)}" alt="{$type.title}" />*}
									</div>
								</div>
								<div class="info">
									<div class="controls"></div>
									<div class="bottom-block">							
										<div class="titles-block">
											<a href="{$type->getUrl()}{$selection_page.key}/" class="title link-target">{$type.title}</a>
											<div class="type">{$count|plural_form:'модель':'модели':'моделей'}</div>
										</div>
									</div>						
								</div>
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
			
			{*include file="components/banners.tpl"*}
			
			<div class="types-bottom clearbox">
				{if !empty($post)}
					<div class="types-col">
						<h3><span class="a-inline-block">Статья</span></h3>
						<div class="types-cont">
							<div class="edited-text">
								Нет описания
							</div>
						</div>
					</div>
				{/if}
				<div class="types-col">
					<h3><span class="a-inline-block">БРЕНДЫ</span></h3>
					<div class="types-cont brands-cont">
						<ul class="brands-list">
							{foreach from=$manufs item=m name=manuf_block}
                                {if empty($m.post) || $m.post.status == 'public' || $m.post.status == 'close'}
                                    <li>
                                        <a href="/catalog/brand/{$m.key}/" title="{$m.title}">
                                            <span class="hatch"></span>
                                            {*<span class="marker m-excl"></span>*}
                                            <img src="{if !empty($m.cover)}{$m.cover.url}{/if}" alt="{$m.title}" />
                                        </a>
                                    </li>
                                {/if}
							{/foreach}
						</ul>
					</div>
				</div>
			</div>
							
		</div>
	</div>
</div>