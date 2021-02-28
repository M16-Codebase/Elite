{?$admin_link = '/discount/listEdit/'}

<div class="promo-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			<div class="aside-catalog-select">
				{include file="components/catalog-menu.tpl"}
			</div>
			{include file="components/company-menu.tpl"}
		</div>
		{include file="components/bonus-block.tpl"}
	</aside>
	<div class="main-col">
		<div class="content-white-block">
			<div class="white-block-inner">
				{include file="components/breadcrumb.tpl"}
				<h1>Акции в нашем магазине</h1>
			</div>
			<div class="grey-block-inner">
				{foreach from=$discounts item=promo name=promo_list}
					<div class="promo-block content-white-block link-wrap{if iteration > 3} more-item a-hidden{/if}">
						{?$cover = $promo.post.gallery->getCover()}
						{if !empty($cover)}
							<div class="promo-images">
								<a href="{$promo->getUrl()}" class="promo-image m-current">
									<img src="{$cover->getUrl()}" alt="{$promo.title}" style="width: {$cover.width}px; height: {$cover.height}px;" />
								</a>
							</div>
						{/if}
						<div class="promo-bottom justify">
							<div class="promo-title col1"><a href="{$promo->getUrl()}" class="link-target">{$promo.post.title}</a></div>
							<div class="promo-descr col2">{$promo.title}</div>
							<div class="main">до {$promo.date_end|date_format_lang:'%d %B %Y':'ru'}</div>
						</div>
					</div>
				{/foreach}
				{if count($discounts) > 3}
					<div class="more-btn">
						<a class="btn btn-grey-blue a-inline-block" href="#" data-alt-text="Скрыть акции">Показать еще акции</a>
					</div>
				{/if}	
			</div>
		</div>
	</div>
</div>
{include file="components/brands.tpl"}
{include file="components/news-block.tpl"}
