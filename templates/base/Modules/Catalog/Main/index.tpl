{if $current_type.title == 'Инструмент'}{?$currentTypeTitle='инструмент'}{else}{?$currentTypeTitle='пожарное оборудование'}{/if}
{?$pageTitle = 'Купить ' . $currentTypeTitle . ' — Интернет-магазин ТехноАльт'}
{?$pageDescription = 'Оформляйте заказ и покупайте ' . $currentTypeTitle . ' в компании ТехноАльт. Доступные цены, доставка по всей России.'}

{?$admin_link = '/catalog-type/?id=' . $current_type.id}
<div class="site-body container clearfix">
	<div class="endless-line m-site-body-var-1"></div>
	<div class="endless-line m-right-side"></div>
	<aside class="page-aside a-left">
		{*{include file='components/sideTypesMenu.tpl'}*}
		{*{include file='components/sidePosts.tpl'}*}
	</aside>
	<div class="page-content a-left">
		<div class="slideshow-screen">
			<div class="img-wrapper flex-img">
				<div class="presentation-cont">
					{if !empty($banners)}
						<div class="presentation small-banner">
							{if count($banners) > 1}<ul class="pr-switcher"></ul>{/if}
							{foreach from=$banners item=banner name=banners}
								{if !empty($banner.destination)}
									<a href="{if $banner.link_type == 'external'}http://{/if}{$banner.destination}" class="pr-image{if first} m-first{/if}"{if $banner.link_type == 'external'} target="_blank"{/if} alt="ТехноАльт" >
								{else}
									<div class="pr-image{if first} m-first{/if}">
								{/if}
								{if $banner.showmode == 'image'}
									<img src="{$banner.image->getUrl(870, 338, 'C')}" alt="ТехноАльт" />
								{else}
									<div class="pr-shadow"></div>
									<div class="pr-text">
										{if !empty($banner.title)}<div class="pr-title grad-text">{$banner.title}</div>{/if}
										{if !empty($banner.description)}<div class="pr-descr"><mark>{$banner.description}</mark></div>{/if}
									</div>
									<img src="{$banner.image->getUrl(870, 338, 'C')}" alt="ТехноАльт" />
								{/if}
								{if !empty($banner.destination)}</a>{else}</div>{/if}
							{/foreach}
						</div>
					{/if}
				</div>	
			</div>
		</div>
		{*{include file='components/goodAndNewItems.tpl'}*}
		<section class="categories-presentation">
			<h2 class="categories-presentation-header">
				{$current_type.title}
			</h2>
			<div class="categories-wrapper">
				<div class="inner-wrapper clearfix">
				{foreach from=$types_children item=type_data name=types_children}
					{?$type=$type_data}
					<article class="single-category a-left">
						{*{?$cover=$type->getCover()}*}
						{*{if $cover}*}
							{*<a href="{$type->getUrl()}"><img src="{$cover->getUrl(220, 160, true)}" class="single-category-img"></a>*}
							{*{else}*}
							{*<a href="{$type->getUrl()}"><img src="{$cover->getDefault(220, 160, true)->getUrl()}" class="single-category-img"></a>*}
						{*{/if}*}
						<h3 class="single-category-header"><a href="{$type->getUrl()}">{$type.title}</a></h3>
						{*<ul class="sub-categories-list">*}
							{*{foreach from=$type_data.types item=child name=children}*}
								{*<li {if first && last || last}class="m-no-bullit"{/if}>*}
									{*<a href="{$child->getUrl()}">{$child.title}</a>*}
								{*</li>*}
							{*{/foreach}*}
						{*</ul>*}
					</article>
					{if iteration%3==0 && !last}
						</div>
						<div class="inner-wrapper clearfix">
					{/if}
				{/foreach}
				</div>
			</div>
		</section>
		{if !empty($current_type.post.text)}
			<section class="promo-good clearfix">
				<h2 class="promo-good-header a-left">
					{if !empty($current_type.annotation)}{$current_type.annotation}{/if}
				</h2>
				<div class="promo-good-description article-page a-left">
					{$current_type.post.text|html}
				</div>
			</section>
		{/if}
		{*{include file='components/lastArticles.tpl'}*}
	</div>
</div>