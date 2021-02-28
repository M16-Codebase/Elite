{?$admin_link = '/discount/edit/?id=' . $discount.id}

<div class="promo-page justify" data-id="{$discount.id}">
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
				{include file="components/breadcrumb.tpl" other_link=array('Акции' => '/discount/')}
				{if !empty($discount)}
					<h1>{$discount.title}</h1>
					<div class="item-promo">
						<div class="promo-date main">до {$discount.date_end|date_format_lang:'%d %B %Y':'ru'}</div>
						<div class="promo-description">
							<p>{$discount.post.title}</p>
						</div>
						{?$cover = $discount.post.gallery->getCover()}
						{if !empty($cover)}
							<div class="promo-image">
								<a href="{$cover->getUrl()}" class="m-current fancybox">
									<img src="{$cover->getUrl()}" alt="{$discount.title}" style="width: {$cover.width}px; height: {$cover.height}px;" />
								</a>
							</div>
						{/if}
					</div>
					{*if !empty($discount.post.annotation)}
						<div class="annotation">{$discount.post.annotation}</div>
					{/if*}
					{if !empty($discount.post.text)}
						<div class="info-block">
							{$discount.post.text|html}
						</div>
					{/if}
				{/if}
			</div>
			<div class="promo-items">
				<div class="promo-top"><div class="marker-cont"><i class="promo-marker"></i></div></div>
				<h1>Товары по акции</h1>
				{?$types_count = 0}
				{capture assign=promo_types_list}
					{foreach from=$types item=promo_type key=type_id}
						{if $promo_type.allow_children == 0}
							{?$types_count++}
							<li class="catalog-item no-touch-hover{if !empty($quicky.get.type_id) && $quicky.get.type_id == $promo_type.id} m-current{/if}" data-id="{$promo_type.id}">
								{if !empty($promo_type.cover)}
									<div class="cover">
										<img src="{$promo_type.cover->getUrl(131,121,90,null,true)}" alt="{$promo_type.title}" />
									</div>
								{/if}
								<div class="info">
									<a href="{$promo_type->getUrl()}" class="title">{$promo_type.title}</a>
								</div>
							</li>
						{/if}
					{/foreach}
				{/capture}
				{if !empty($types) && $types_count > 1}
					<div class="white-block-bay">
						<div>Выберите товарную категорию</div>
					</div>				
					<div class="types-tabs white-block-inner carousel" data-step=2 data-scope=5 data-speed=100>
						{if $types_count > 4}
							<div class="blue-arrows">
								<div class="car-prev a-left">
									<div class="arrow arrow-left"></div>
								</div>
								<div class="car-next a-right">
									<div class="arrow arrow-right"></div>
								</div>
							</div>
						{/if}
						<div class="car-wrap">
							<ul class="main-catalog-types a-inline-cont">
								{$promo_types_list|html}
							</ul>
						</div>
						{if $types_count > 4}
							<div class="carousel-dots-cont">
								<ul class="carousel-dots car-pages a-inline-cont a-inline-block">
								</ul>
							</div>
						{/if}
					</div>
				{/if}	
			</div>
			{if !empty($items)}
				<div class="white-block-inner">					
					<div class="catalog-block">
						{include file="Modules/Catalog/Main/itemsList.tpl" catalog_items=$items}	
					</div>
				</div>
			{/if}
		</div>
	</div>
</div>
{include file="components/brands.tpl"}
{include file="components/news-block.tpl"}





{*{$discount.title}<br />
{$discount.post.text}<br />
{?$gallery = $discount.post.gallery}*}