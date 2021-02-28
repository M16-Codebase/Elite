<div class="field m-object m-gallery">
	{if empty($object)}
		
		<div class="add-row row m-fullwidth">
			<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-add"></i> <span class="small-descr">Добавить изображение</span>
			</div>
			<div class="w9"></div>
		</div>
		
	{else}
			
		<div class="add-row row m-fullwidth a-hidden">
			<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-add"></i> <span class="small-descr">Добавить изображение</span>
			</div>
			<div class="w9"></div>
		</div>
		<div class="row object-prop">
			<div class="w2">
				{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
				<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" data-val-id="{$prop_val_id}" />
				<div class="row-gallery">
					<a href="{$object->getUrl()}" class="fancybox row-image" rel="img-{$object.id}">
						<img src="{$object->getUrl(70, 70, true)}" alt="{$property.key}" />
					</a>
				</div>
			</div>
			<div class="w9">
				<a href="{$object->getUrl()}" target="_blank">
					{if !empty($object.title)}{$object.title}{else}{$object.id}.{$object.ext}{/if}
				</a>
				{if !empty($object.text)}
					<div class="descr">{$object.text}</div>
				{/if}
			</div>
			<div class="edit-object action-button w05" data-object_id="{$object.id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-prop-edit"></i>
			</div>
			<div class="delete-object action-button w05">
				<i class="icon-prop-delete"></i>
			</div>
		</div>
		
	{/if}
</div>