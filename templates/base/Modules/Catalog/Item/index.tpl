{?$currentCatalog = $current_type->getCatalog()}
{*{?$pageTitle = $current_type.title . ' — ' . $currentCatalog.word_cases['i']['2']['i']}*}
{?$pageTitle = $current_type.title . ' — ' . (!empty($confTitle) ? $confTitle : '')}
{?$site_link = $current_type->getUrl()}

{include file="Modules/Catalog/Item/itemFilter.tpl" assign=aside_filter}
<div class="content-top">
	<form class="select-type blue-block">
        {if !empty($all_types_by_levels)}
            <select name="type_id">
				{foreach from = $all_types_by_levels key=type_id item=type}
					<option{if $type.level} data-before="<span class='tree a-inline-block' style='width: {$type.level * 14}px'></span>"{/if} class="level{$type.level+1}" value="{$type_id}"{if !empty($current_type.id) && $current_type.id == $type_id} selected{/if}>{$type.data.title}</option>
				{/foreach}
            </select>
        {/if}
	</form>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			multiple = true
			buttons = array(
				'back' => '/catalog-admin/',
				'add' => ($account->isPermission('catalog-item', 'edit') && $current_type.allow_children ==0 ? '/catalog-item/edit/?type_id=' . $current_type.id : 0),
				'show' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
					'inactive' => 1,
				) : 0),
				'hide' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
					'inactive' => 1,
				) : 0),
				'delete' => ($account->isPermission('catalog-item', 'delete')? array(
					'inactive' => 1,
				) : 0),
				'edit' => ($account->isPermission('catalog-item', 'changeItemProp')? array(
					'inactive' => $catalog_items_count? 0 : 1
				) : 0),
				'type' => ($account->isPermission('catalog-type')? '/catalog-type/catalog/?id=' . $current_type.id : 0)
			)}
	</div>
</div>

<div class="content-scroll">
	<form class="actions-cont items-edit viewport">
		<div class="items-list" data-count="{$catalog_items_count}">
			{include file="Modules/Catalog/Item/listItems.tpl" catalog_items_count={$catalog_items_count} without_filter=true}
		</div>
	</form>
	{if !empty($current_type_filter)}
		{$current_type_filter|html}
	{/if}
</div>

{include file="/Modules/Catalog/Item/edit_single_prop.tpl" assign=edit_single_prop}
{include file="/Modules/Catalog/Item/get_ids.tpl" assign=get_ids}
{capture assign=editBlock name=editBlock}
	{$edit_single_prop|html}
	{$get_ids|html}
{/capture}



{*include file="Admin/popups/import_items.tpl"*}