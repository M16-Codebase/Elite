{if !empty($current_type.properties)}
    {include file="Modules/Catalog/Item/edit_item_properties.tpl" item_properties=$current_type.properties config_props=true}
{/if}
<input type="submit" class="a-hidden" />