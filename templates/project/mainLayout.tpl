<div class="page-wrap">
	{include file="components/header.tpl"}
	<section class="page-main">  
		{$moduleResult|html}  
	</section>   
	{include file="components/footer.tpl"} 
</div>   
{?$site_admin_urls = array(
		"real-estate" => array(
			"index" => ($account->isPermission('catalog-type') ? "/catalog-type/catalog/?id=13" : "/site/"),
			"complexPage" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($item.id) ? $item.id : '') : "/site/"),
			"informationBlock" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($complex.id) ? $complex.id : '') : "/site/"),
			"flatPage" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($item.id) ? $item.id : '') : "/site/"),
			"apartments" => ($account->isPermission('catalog-type') ? "/catalog-type/catalog/?id=16" : "/site/"),
			"scheme" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($complex.id) ? $complex.id : '') : "/site/"),
		),
		"resale" => array(
			"items" => ($account->isPermission('catalog-type') ? "/catalog-type/catalog/?id=17" : "/site/"),
			"viewItem" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($item.id) ? $item.id : '') : "/site/"),
		),
		"district" => array(
			"index" => ($account->isPermission('catalog-type') ? "/catalog-type/catalog/?id=10" : "/site/"),
			"viewItem" => ($account->isPermission('catalog-item') ? "/catalog-item/edit/?id=" . (!empty($item.id) ? $item.id : '') : "/site/"),
		),
)}
{if $accountType == 'Admin' || $accountType == 'Broker' || $accountType == 'SeoAdmin' || $accountType == 'SuperAdmin'}
	<a target="_blank" href="{if !empty($site_admin_urls[$moduleUrl][$action]) }{$site_admin_urls[$moduleUrl][$action]}{else}/site/{/if}" title="Управление сайтом" class="admin-icon m-shown">
		{fetch file=$path . "settings.svg"}
	</a> 
{/if}
{if $action != 'favorites'}
	<a href="{$url_prefix}/favorites/" title="{$lang->get('Избранные предложения', 'Favorite offers collection')}" class="favorite-icon{if !empty($favorites_count)} m-shown{/if}">
		<div class="num"><span>{$favorites_count}</span></div>
		{fetch file=$path . "favorite.svg"}
	</a>
{/if} 