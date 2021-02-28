<h1>Редактирование свойства «{$property.title}»</h1>

{?$back_link = '/catalog-item/edit/?id='}
{if $property.multiple}
    {?$catalog_item = $entity->getItem()}
    {?$back_link .= $catalog_item.id . '&tab=variants&v=' . $entity.id}
{else}
    {?$catalog_item = $entity}
    {?$back_link .= $catalog_item.id}
{/if}
{?$current_type = $catalog_item->getType()}

{include file='Admin/components/actions_panel.tpl' buttons=array(
    'back' => $back_link,
    'save' => '#'
)}
<form id="edit-prop-form" method="POST" action="/catalog-item/editEntityPropertyOfItem/">
    <input type="hidden" name="entity_id" value="{$entity.id}">
    <input type="hidden" name="property_id" value="{$property.id}">
    {if count($segments) > 1}
        <select name="segment_id">
            {foreach from=$segments item=s}
                <option value="{$s.id}">{$s.title}</option>
            {/foreach}
        </select>
    {else}
        <input type="hidden" name="segment_id" value="">
    {/if}
    <div id="obj-edit-block">
        {if count($segments) <= 1}
            {include file='Modules/Catalog/Item/editEntityPropertyOfItem.tpl'}
        {/if}
    </div>
</form>