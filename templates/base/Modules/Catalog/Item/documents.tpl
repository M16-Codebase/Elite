{?$site_link = '/'}
{?$site_link_en = '/en/'}

<form method="GET" class="actions-list items_edit" data-group="{$group}">
	{include file="Admin/components/actions_panel.tpl" 
		multiple = true
		buttons = array(
			'back' => '/site/',
			'add' => $account->isPermission('catalog-item', 'edit')? array(
				'url' => '/catalog-item/create/?type_id=' . $current_type.id,
				'text' => 'Новый документ'
			) : '',
			'delete' => $account->isPermission('catalog-item', 'delItems')? array('inactive' => 1) : ''
		)}
	<div class="result-header header-objects">
		{if $account->isPermission('catalog-item', 'edit')}
			<div class="filter-check a-left">
				<input type="checkbox" value="" class="check-all" />
			</div>
		{/if}
		<div class="order-select dropdown hoverable">
			<div class="header-menu-link dd-arrow a-link">				
				{if empty($smarty.get.order.data_zakljuchenija)}
					Сортировка по дате заключения от новых к старым
				{else}
					Сортировка по дате заключения от старым к новых
				{/if}								
			</div>
			<ul class="dropdown-menu dd-list a-hidden">
				<li><a href="/catalog-item/?group={$group}&id={$current_type.id}&order[data_zakljuchenija]=0" class="sort-link" data-order="data_zakljuchenija" data-val="0">Сортировка по дате заключения от новых к старым</a></li>
				<li><a href="/catalog-item/?group={$group}&id={$current_type.id}&order[data_zakljuchenija]=1" class="sort-link" data-order="data_zakljuchenija" data-val="1">Сортировка по дате заключения от старым к новых</a></li>
			</ul>
		</div>		
		<div class="filter-result a-right">
			{if !empty($count)}
				{$count|plural_form:'Найден':'Найдено':'Найдено':false} {$count|plural_form:'документ':'документа':'документов'}
			{/if}
		</div>
	</div>
	<div class="main-content-gray offer-body documents-page">
		 {if !empty($catalog_items)}
			<input type="hidden" name="page" value="{!empty($smarty.get.page) ? $smarty.get.page : 1}" />
			<ul class="offers-list-ungrouped offer-body">
				{include file="Modules/Catalog/Item/Documents/listItems.tpl" without_filter=true}								
			</ul>
			{include file="Admin/components/paging.tpl" show=5}
			{if !empty($current_type_filter)}
				{$current_type_filter|html}
			{/if}
		{else}
			<div class="empty-list">
				Нет документов
			</div>
		{/if}	
	</div>
</form>