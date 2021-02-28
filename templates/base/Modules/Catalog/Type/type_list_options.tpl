{if !empty($types)}
	{foreach from=$types item=$type}
		<option value="{$type.id}"{if $type.allow_children} disabled{/if}>{$sep|html}{$type.title}</option>
		{if $type.allow_children && !empty($types_by_parents[$type.id])}
			{include file="Modules/Catalog/Admin/type_list_options.tpl" types=$types_by_parents[$type.id] sep=$sep . '&mdash;&nbsp;'}
		{/if}
	{/foreach}
{/if}