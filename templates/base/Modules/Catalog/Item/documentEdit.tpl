{*?$segments = array(1 => $segments[1])*}
{?$site_link = '/'}
{?$site_link_en = '/en/'}

<h1>
	{if empty($catalog_item.nomer_dogovora)}
		Новый документ
	{else}
		Документ №{$catalog_item.nomer_dogovora}
	{/if}
</h1>
{if !empty($catalog_item.juridicheskoe_litso)}
	<div class="item-location">
		{$catalog_item.juridicheskoe_litso}
	</div>
{/if}

<div class="result-header header-object">
	{if !empty($catalog_item.data_zakljuchenija) || !empty($catalog_item.data_okonchanija_sroka_dejstvija)}
		{if !empty($catalog_item.data_okonchanija_sroka_dejstvija)}
			{?$actual_date = floor(($catalog_item.properties.data_okonchanija_sroka_dejstvija.value - time())/(60*60*24))+1}
		{else}
			{?$actual_date = 1}
		{/if}
		<div class="offer-date{if $actual_date < 0} no-actual{/if}">
			<i class="i-date{if $actual_date < 0} no-actual{/if}"></i>
			{if !empty($catalog_item.data_zakljuchenija)}{$catalog_item.data_zakljuchenija}{/if}
			{if !empty($catalog_item.data_zakljuchenija) && !empty($catalog_item.data_okonchanija_sroka_dejstvija)} — {/if}
			{if !empty($catalog_item.data_okonchanija_sroka_dejstvija)}{$catalog_item.data_okonchanija_sroka_dejstvija}{/if}
		</div>
	{/if}
	{if !empty($catalog_item.document_status)}
		<div class="offer-status {if $catalog_item.document_status=='Подписан'}contract{else}project{/if}">
			{$catalog_item.document_status}
		</div>
	{elseif $current_type.id != 62}
		<div class="offer-status non-status">Статус не определен</div>
	{/if}
	{if $account->isPermission('catalog-item', 'edit')}
		<div class="item-view-type a-right">
			<a href="/catalog-item/edit/?id={$catalog_item.id}" class="item-edit m-current"></a>
			<a href="/catalog-view/?id={$catalog_item.id}" class="item-view"></a>
		</div>
	{/if}
</div>

<div class="action-panel-cont">
	{include file="Admin/components/actions_panel.tpl"
		buttons = array(
			'back' => ('/catalog-item/?id=' . $catalog_item.type_id),
			'delete' => '#'
		)}
</div>

{if !empty($catalog_item)}
	<div class="tabs-wrap">
		<ul id="tabs" data-item_id="{$catalog_item.id}">
			{?$req_tab = !empty($smarty.get.tab)? $smarty.get.tab : 'options'}
			<li><a data-tab="options" href="/catalog-item/edit/?id={$catalog_item.id}&tab=options" class="{if $req_tab == 'options'}m-current{/if}">Документ</a></li>
		</ul>
		<div id="tabs-pages" data-type-id="{$catalog_item.type_id}" data-item-id="{$catalog_item.id}" class="main-content-gray">
			<div id="options" class="tab-page aside-controls{if $req_tab == 'options'} m-current{/if}">
				<form class="edit_properties_form" action="/catalog-item/saveJson/" data-id="{$catalog_item.id}">
					<div class="aside-panel">
						<div class="aside-float">
							{include file="Admin/components/actions_panel.tpl" buttons = array('save' => 1)}
						</div>
					</div>
					<div>
						{if !empty($item_properties)}
							{include file="Modules/Catalog/Item/Documents/edit_item_properties.tpl" item_properties=$item_properties variant_list=false create=true}
						{/if}
						<input type="submit" class="a-hidden" />
					</div>
				</form>
			</div>
		</div>
	</div>
{else}
	<div class="empty-list">Такого документа нет</div>
{/if}