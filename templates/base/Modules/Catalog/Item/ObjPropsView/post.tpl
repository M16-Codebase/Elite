{if $property.segment == 1}
	{?$segments_data = ''}
	{foreach from=$segments item=segment name=segments}
		{?$segments_data .= ' data-segment-' . iteration . '=' . $segment.id}
	{/foreach}
{else}
	{?$segments_data = 'data-segment_id=' . $segment_id}
{/if}
<div class="field sortable m-object{if empty($objects)} m-post{/if}{if $property.segment == 1} segment-object{/if}" data-notsend="1" data-items=".object-prop">
	{if empty($object) && empty($objects)}
		
		<div class="add-row row m-fullwidth">
			<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}"{$segments_data}>
				<i class="icon-add"></i> <span class="small-descr">Создать статью</span>
			</div>
			<div class="w9"></div>
		</div>
		
	{else}
		
		{if !empty($objects)}
			
			{if $property.segment == 1}
				
				{?$objects_by_pos = array()}
				{?$objects_val_id = array()}
				{foreach from=$segments item=segment}
					{?$entity->setSegment($segment.id)}
					{if !empty($entity.properties[$property.key]['value'])}
						{foreach from=$entity.properties[$property.key]['value'] item=obj_id key=val_id name=entity_properties}
							{if !empty($objects[$segment.id][$obj_id])}
								{if empty($objects_by_pos[iteration-1])}{?$objects_by_pos[iteration-1] = array()}{/if}
								{?$objects_by_pos[iteration-1][$segment.id] = $objects[$segment.id][$obj_id]}
								{?$objects_val_id[$obj_id] = $val_id}
							{/if}
						{/foreach}
					{/if}
				{/foreach}
				{?$object = null}
				{include file="Modules/Catalog/Item/ObjPropsViewrow/post.tpl"}
				
			{else}
				
				{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
				{if !empty($prop_val_id)}
					{?$prop_obj = $entity['properties'][$property.key]['complete_value']}
					{?$prop_val = $entity['properties'][$property.key]['value']}
					{foreach from=$prop_val_id item=val_id key=val_i}
						{?$obj_id = $prop_val[$val_id]}
						{?$object = $prop_obj[$obj_id]}
						{include file="Modules/Catalog/Item/ObjPropsViewrow/post.tpl" object=$object val_id=$val_id}
					{/foreach}
				{/if}
				
			{/if}
			
			<div class="add-row row m-fullwidth">
				<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}"{$segments_data}>
					<i class="icon-add"></i> <span class="small-descr">Добавить статью</span>
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
			{?$val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
			<div class="add-row row m-fullwidth a-hidden">
				<div class="add-object add-btn w3" data-entity_id="{$entity.id}" data-property_id="{$property.id}"{$segments_data}>
					<i class="icon-add"></i> <span class="small-descr">Добавить статью</span>
				</div>
				<div class="w9"></div>
			</div>
			<div class="row object-prop">
				<div class="w2">
					<div class="row-cover m-border"><i class=""></i></div>
				</div>
				{if $property.segment == 1}
					{?$objects = $object}
					{foreach from=$segments item=segment}
						{if !empty($objects[$segment.id])}
							{?$object = $objects[$segment.id]}
							<a href="{$object->getUrl()}" target="_blank" class="w45">
								<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" data-segment="{$segment.id}"{if !empty($val_id[$segment.id])} data-val-id="{$val_id[$segment.id]}"{/if} />
								<span class="title">{if !empty($object.title)}{$object.title}{else}Нет заголовка{/if}</span>
							</a>
						{else}
							<div class="w5">
								<span class="small-descr empty-segment-object" data-segment="{$segment.id}">Статья не создана</span>
							</div>
						{/if}
					{/foreach}
				{else}
					<div class="w9">
						<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" {if !empty($val_id)} data-val-id="{$val_id}"{/if}/>
						<a href="{$object->getUrl()}" target="_blank">{if !empty($object.title)}{$object.title}{else}Нет заголовка{/if}</a>
						{if !empty($object.annotation)}
							<div class="descr">{$object.annotation}</div>
						{/if}
					</div>
				{/if}
				<div class="edit-object action-button w05"{if $property.segment != 1} data-object_id="{$object.id}" data-segment_id="{$segment_id}"{/if} data-entity_id="{$entity.id}" data-property_id="{$property.id}">
					<i class="icon-prop-edit"></i>
				</div>
				<div class="delete-object action-button w05">
					<i class="icon-prop-delete"></i>
				</div>
			</div>
			
		{/if}
		
	{/if}
</div>