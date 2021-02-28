<div class="field sortable m-object" data-notsend="1" data-items=".object-prop">
	{if empty($object) && empty($objects)}
		
		<div class="add-row row m-fullwidth">
			<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
				<i class="icon-add"></i> <span class="small-descr">Добавить файл</span>
			</div>
			<div class="w9"></div>
		</div>
		
	{else}
		{if !empty($objects)}			
			
			{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
			{if !empty($prop_val_id)}
				{?$prop_obj = $entity['properties'][$property.key]['complete_value']}
				{?$prop_val = $entity['properties'][$property.key]['value']}
				{foreach from=$prop_val_id item=val_id key=val_i}
					{?$obj_id = $prop_val[$val_id]}
					{?$object = $prop_obj[$obj_id]}
					{include file="Modules/Catalog/Item/ObjPropsViewrow/file.tpl" object=$object val_id=$val_id}
				{/foreach}
			{/if}
			<div class="add-row row m-fullwidth">
				<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
					<i class="icon-add"></i> <span class="small-descr">Добавить файл</span>
				</div>
				<div class="w8"></div>
				<div class="w1">
					<div class="prop-menu dropdown">
						<div class="dropdown-toggle">
							<i class="icon-prop-more"></i>
						</div>
						<ul class="dropdown-menu a-hidden">
							<li><a href="#" class="delete-all">Удалить все</a></li>
							<li><a href="#" class="sort-alph">Отсортировать по алфавиту</a></li>
						</ul>
					</div>
				</div>
			</div>
			
		{elseif !empty($object)}
			
			<div class="add-row row m-fullwidth a-hidden">
				<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
					<i class="icon-add"></i> <span class="small-descr">Добавить файл</span>
				</div>
				<div class="w9"></div>
			</div>
			<div class="row object-prop">
				<div class="w2">
					{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
					<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" data-val-id="{$prop_val_id}" />
					{if !empty($object.ext)}
						<a href="{$object->getUrl('relative')}" class="row-cover m-border" target="_blank">.{$object.ext}</a>
					{/if}
				</div>
				<div class="w9">
					{if !empty($object.title)}
						<a href="{$object->getUrl('relative')}" target="_blank">{$object.title}</a> <span class="descr">— {$object.full_name}</span>
					{else}
						<a href="{$object->getUrl('relative')}" target="_blank">{$object.full_name}</a>
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
		
	{/if}
</div>