{if $property.segment == 1}
	
	{if !empty($objects_by_pos)}
		{foreach from=$objects_by_pos item=objects_row key=pos}
			<div class="row object-prop m-saved m-fullwidth">
				<div class="drag-drop w05"></div>
				{foreach from=$segments item=segment}
					{if !empty($objects_row[$segment.id])}
						{?$object = $objects_row[$segment.id]}
						<a href="{$object->getUrl()}" target="_blank" class="w5">
							<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" data-segment="{$segment.id}"{if !empty($objects_val_id[$object.id])} data-val-id="{$objects_val_id[$object.id]}"{/if} />
							<span class="title">{if !empty($object.title)}{$object.title}{else}Нет заголовка{/if}</span>
						</a>
					{else}
						<div class="w5">
							<span class="small-descr empty-segment-object" data-segment="{$segment.id}">Статья не создана</span>
						</div>
					{/if}
				{/foreach}
				<div class="w05"></div>
				<div class="edit-object action-button w05" data-entity_id="{$entity.id}" data-property_id="{$property.id}">
					<i class="icon-prop-edit"></i>
				</div>
				<div class="delete-object action-button w05">
					<i class="icon-prop-delete"></i>
				</div>
			</div>
		{/foreach}
	{elseif !empty($object)}
		{?$objects = $object}
		<div class="row object-prop m-saved m-fullwidth">
			<div class="drag-drop w05"></div>
			{foreach from=$segments item=segment}
				{if !empty($objects[$segment.id])}
					{?$object = $objects[$segment.id]}
					<a href="{$object->getUrl()}" target="_blank" class="w5">
						<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" data-segment="{$segment.id}"{if !empty($val_id[$segment.id])} data-val-id="{$val_id[$segment.id]}"{/if} />
						<span class="title">{if !empty($object.title)}{$object.title}{else}Нет заголовка{/if}</span>
					</a>
				{else}
					<div class="w5">
						<span class="small-descr empty-segment-object" data-segment="{$segment.id}">Статья не создана</span>
					</div>
				{/if}
			{/foreach}
			<div class="w05"></div>
			<div class="edit-object action-button w05" data-entity_id="{$entity.id}" data-property_id="{$property.id}">
				<i class="icon-prop-edit"></i>
			</div>
			<div class="delete-object action-button w05">
				<i class="icon-prop-delete"></i>
			</div>
		</div>
	{/if}

{elseif !empty($object)}
	<div class="row object-prop m-saved m-fullwidth">
		<div class="drag-drop w05"></div>
		<a href="{$object->getUrl()}" target="_blank" class="w10">
			<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}"{if !empty($val_id)} data-val-id="{$val_id}"{/if} />
			<span class="title">{if !empty($object.title)}{$object.title}{else}Нет заголовка{/if}</span>
		</a>
		<div class="w05"></div>
		<div class="edit-object action-button w05" data-object_id="{$object.id}" data-segment_id="{$segment_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}">
			<i class="icon-prop-edit"></i>
		</div>
		<div class="delete-object action-button w05">
			<i class="icon-prop-delete"></i>
		</div>
	</div>

{/if}