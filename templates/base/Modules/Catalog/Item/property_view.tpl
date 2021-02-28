{if !empty($property)}
	{if $property['multiple'] == 1 && empty($variant_list)}
		{?$current_entity = !empty($special_variant) ? $special_variant : NULL}{*при создание нет спец варианта*}
		{?$lang_values = $lang_props_spec}
	{else}
		{?$current_entity = $entity}
		{?$lang_values = $lang_props}
	{/if}
	{?$prop_val_id = !empty($current_entity['properties'][$property.key]['val_id']) ? $current_entity['properties'][$property.key]['val_id'] : null}
	{?$prop_val = !empty($prop_val_id) ? $current_entity['properties'][$property.key]['value'] : null}
	{* Общие случаи *}
    {if !$property->instanceofDataType('entity') || !empty($editing_variant_id) || (!empty($catalog_item) && !empty($catalog_item.id))}
		{if $constants.segment_mode == 'lang'}
			{if $property.segment == 1}
				{include file="Modules/Catalog/Item/property_common_cases_lang_values.tpl"}
			{else}
				{include file="Modules/Catalog/Item/property_common_cases_lang.tpl"}
			{/if}
		{else}
			{include file="Modules/Catalog/Item/property_common_cases.tpl"}
		{/if}
    {/if}
{/if}