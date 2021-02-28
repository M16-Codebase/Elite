{if !empty($property)}
	<a class="edit-entity-property-btn" href="/catalog-item/editObjPropertyPage/?entity_id={if !empty($editing_variant_id)}{$editing_variant_id}{elseif !empty($catalog_item)}{$catalog_item.id}{/if}&property_id={$property.id}">редакт</a>
{/if}