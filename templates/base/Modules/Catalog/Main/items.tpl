{?$section_type = $current_type->getParent()}
{?$pageTitle = $current_type.title . ' купить — ' . $section_type.title . ' — ТехноАльт'}
{?$pageDescription = $current_type.title . ' купить в компании ТехноАльт — ' . $section_type.title . ' с доставкой по всей России. Оформить заказ можно в нашем интернет-магазине.'}

{?$admin_link = '/catalog-item/?id=' . $current_type.id}
<div class="site-body container clearfix">
	<div class="endless-line m-site-body-var-1"></div>
	<div class="endless-line m-right-side"></div>
	<aside class="page-aside a-left">
		{include file="components/breadcrumb.tpl"}
		{include file="Modules/Catalog/Main/itemsFilter.tpl"}
		<div class="related-links">
			<div class="help-message">
				<div class="help-announce">
					Если вы затрудняетесь с выбором — звоните нам, мы поможем!
				</div>
				<div class="provider-phone">
				</div>
			</div>
		</div>				
		{if !empty($current_type.post.text)}
			<div class="category-description">
				<h3 class="category-description-header">
					{$current_type.post.annotation} 
				</h3>
				<div class="article-page">
					{$current_type.post.text|html} 
				</div>
			</div>
		{/if}	
	</aside>
	<div class="page-content items-list-cont a-left">
		{?$showCategory=1}
		{include file="Modules/Catalog/Main/itemsList.tpl"}
		{?$showCategory=0}
	</div>
</div>