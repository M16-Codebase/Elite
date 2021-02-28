{?$site_link = '/'}
{?$site_link_en = '/en/'}


<h1>
    Документ{if !empty($catalog_item.nomer_dogovora)} №{$catalog_item.nomer_dogovora}{/if}
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
			<a href="/catalog-item/edit/?id={$catalog_item.id}" class="item-edit"></a>
			<a href="/catalog-view/?id={$catalog_item.id}" class="item-view m-current"></a>
		</div>
	{/if}
</div>

<div class="action-panel-cont">
	{include file="Admin/components/actions_panel.tpl"
		buttons = array(
			'back' => ('/catalog-item/?id=' . $catalog_item.type_id)
		)}
</div>

{if !empty($type_properties)}
	{?$end_table = false}
	{?$group = 0}
	{?$current_group = 0}
	{?$prop_i = 0}
	{include file ="Admin/components/view_entity/view_table_properties.tpl"}
	{foreach from=$type_properties item=$item_prop name=item_props}
		{if (isset($catalog_item['properties'][$item_prop.key]['real_value']) && $catalog_item['properties'][$item_prop.key]['real_value'] != '') ||
			(isset($catalog_item['special_variant']['properties'][$item_prop.key]['real_value']) && $catalog_item['special_variant']['properties'][$item_prop.key]['real_value'] != '')}
			{?$end_table = true}
			{?$current_group = $item_prop.group_id}
			{if $group != $current_group}
				{if $group != 0}						
					{?$prop_i = 0}
							</table>
						</div>
					</div>
				{/if}
				<div class="item-view-block slide-box m-open" id="view-{$item_prop.group.key}" data-group-id="{$item_prop.group_id}">
					<div class="slide-body table-cont">
						<table class="spec-table show-table">
			{/if}
			{include file ="Admin/components/view_entity/table_properties.tpl"}
			{?$group = $item_prop.group_id}
		{/if}			
	{/foreach}
	{if $end_table = true}
				</table>
			</div>
		</div>
	{/if}
{/if}