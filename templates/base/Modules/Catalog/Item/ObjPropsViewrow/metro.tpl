{if !empty($object)}
	{*<div class="row object-prop m-saved m-fullwidth">
		<div class="drag-drop w05">
			<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}"{if !empty($val_id)} data-val-id="{$val_id}"{/if} />
		</div>
		<div class="w3">
			<span>{if !empty({$property.key})}{$property.key}{/if}</span>
		</div>
		<div class="w3">
			<span></span>
		</div>
		<div class="w3">
			<span></span>
		</div>
		<div class="w05"></div>
		<div class="edit-object action-button w05" data-object_id="{$object.id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
			<i class="icon-prop-edit"></i>
		</div>
		<div class="delete-object action-button w05">
			<i class="icon-prop-delete"></i>
		</div>
	</div>*}
	<div class="multi-item row">
		<div class="drag-drop w05 ui-sortable-handle"></div>
		<div class="lang-col  w55">
			<input type="text" name="kyg" class="title" value="ыап" data-val-id="449" data-segment="1">
		</div>
		<div class="lang-col en-col w5">
			<input type="text" name="kyg" class="title" value="dsfsdf" data-val-id="450" data-segment="2">
		</div>
		<div class="delete-item action-button w1" title="Удалить" data-text="">
			<i class="icon-prop-delete"></i>
		</div>
	</div>
{/if}