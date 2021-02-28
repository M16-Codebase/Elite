{include file="Modules/Catalog/Item/listItems.tpl" ens_search=true without_filter=true}
{if !empty($current_type_filter)}
	{$current_type_filter|html}
{/if}