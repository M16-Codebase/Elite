<div class="field m-object m-gallery">
	{if empty($object)}
		
		<div class="add-row row m-fullwidth">
			<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-add"></i> <span class="small-descr">Добавить изображения</span>
			</div>
			<div class="w9"></div>
		</div>
		
	{else}
		
		{?$obj_images = $object->getImages()}
		<div class="row object-prop{if empty($obj_images)} a-hidden{/if}">
			<div class="w11">
				<input type="hidden" name="{$property.key}" value="{$object->getId()}" />
				<div class="row-gallery">
					{include file="Modules/Images/Admin/fileList.tpl" images=$obj_images simple=true}
				</div>
			</div>
			<div class="edit-object action-button w1" data-object_id="{$object->getId()}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-prop-edit"></i>
			</div>
		</div>
		<div class="add-row row m-fullwidth">
			<label class="add-btn add-image w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<input type="file" name="image" multiple class="hidden-input not-send" />
				<i class="icon-add"></i> <span class="small-descr">Добавить изображение</span>
			</label>
			<div class="w9">
				<input type="hidden" name="id" value="{$object->getId()}" class="not-send" />
			</div>
		</div>
		
	{/if}
</div>