{if !empty($object)}
	<div class="row object-prop m-fullwidth">
		<div class="drag-drop w05">
			<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}"{if !empty($val_id)} data-val-id="{$val_id}"{/if} />
		</div>
		<div class="w10">
			{if !empty($object.title)}
				<a href="{$object->getUrl('relative')}" target="_blank" class="title">{$object.title}</a> <span class="descr">— {$object.full_name}</span>
			{else}
				<a href="{$object->getUrl('relative')}" target="_blank" class="title">{$object.full_name}</a>
			{/if}
		</div>
		<div class="w05"></div>
		<div class="edit-object action-button w05" data-object_id="{$object.id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
			<i class="icon-prop-edit"></i>
		</div>
		<div class="delete-object action-button w05">
			<i class="icon-prop-delete"></i>
		</div>
	</div>
{/if}