{?$search_string = !empty($search_string) ? $search_string : ''}
{?$pageTitle = 'Результаты поиска по запросу "' . $search_string . '" — ТехноАльт'}
<!--<div class="site-body container clearfix">
	<aside class="page-aside a-left">
		<h2 class="page-aside-header grad-text">Результаты поиска</h2>	
		<div class="search-results-info">
			<h3 class="search-results-header">Найдено в разделах</h3>
			<ul>
				<li class="m-choosen-result"><span class="result-name">Сверла</span>&nbsp;<span class="result-amount">156</span><span class="remove-search-result"></span></li>
				<li><span class="result-name">Метчики</span>&nbsp;<span class="result-amount">1800</span></li>
				<li><span class="result-name">Патроны</span>&nbsp;<span class="result-amount">54</span></li>
				<li><span class="result-name">Плашки</span>&nbsp;<span class="result-amount">2</span></li>
			</ul>
		</div>
	</aside>
</div>-->
<div class="site-body container clearfix">
	<div class="endless-line m-site-body-var-1"></div>
	<div class="endless-line m-right-side"></div>
	<aside class="page-aside a-left">
		<h2 class="page-aside-header grad-text">Результаты поиска</h2>
		<div class="search-results-info">
            {if isset($count)}
			<h3 class="search-results-header">Найдено в разделах</h3>
			<ul class="aside-catalog-types" data-default_id="{$searched_type}">
                            {?$search_link = '/catalog/search/?search='.$search_string}
                            {foreach from=$found_types key=t_id item=ft}
                                    {if !$found_types[$t_id].allow_children && !empty($count_by_types[$t_id]) && $t_id != $searched_type}
                                    <li class="vm-item{if $searched_type == $t_id} m-current{/if}" data-id="{$t_id}">
                                            <a class="resul-name" href="{$search_link}&type_id={$t_id}">{$ft.title}</a>&nbsp;<span class="result-amount">{$count_by_types[$t_id]}</span><span class="remove-type"></span>
                                    </li>
                                    {/if}
                            {/foreach}
				{*<li class="vm-item m-choosen-result"><a class="result-name">Сверла</a>&nbsp;<span class="result-amount">156</span><span class="remove-search-result"></span></li>
				<li class="vm-item"><a class="resul-name">Метчики</a>&nbsp;<span class="result-amount">1800</span></li>
				<li class="vm-item"><a class="resul-name">Патроны</a>&nbsp;<span class="result-amount">54</span></li>
				<li class="vm-item"><a class="resul-name">Плашки</a>&nbsp;<span class="result-amount">2</span></li>*}
			</ul>
            {/if}
		</div>
		{*<div class="print-version-box">
		Версия для печати    
		</div>*}
		{?$print_link = $smarty.server.REQUEST_URI}
		{?$print_link = $print_link . (strpos($print_link, '?') !== false ? '&' : '?') . 'print'}
		<div class="related-links">
			{if !empty($catalog_items)}<a href="{$print_link}" class="icon-text print-it"><i class="icon i-print"></i><span>Версия для печати</span></a>{/if}
			<div class="help-message">
				<div class="help-announce">
					Если вы затрудняетесь с выбором — звоните нам, мы поможем!
				</div>
				<div class="provider-phone">
					{$site_config.office_contacts.phone[0]}
				</div>
			</div>
		</div>
	</aside>
	<div class="page-content a-left">
			{?$showCategory=1}
			{include file="Modules/Catalog/Main/itemsList.tpl"}
			{?$showCategory=0}
	</div>
</div>
	
<div class="popup-window popup-select-variant" data-class="white-popup" data-width="1100"></div>
<div class="popup-window popup-add-variant-to-cart" data-class="white-popup" data-width="520"></div>