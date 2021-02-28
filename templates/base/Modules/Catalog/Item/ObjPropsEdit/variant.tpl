<div class="field sortable m-object" data-notsend="1" data-items=".object-prop">
	{if empty($object) && empty($objects)}
		
		{if $property.values.edit_mode == 'list'}
			<select class="input-values" name="{$property.key}">
				<option value="" selected>Выберите...</option>
				{foreach from=$entities_list item=variant}
					{?$title = $variant.variant_title}
					<option value="{$variant.id}">{$title}</option>
				{/foreach}
			</select>
		{else}
			
			<div class="row">
				<div class="w11">
					<input type="text" class="input-values" name="{$property.key}" placeholder="ID {if $property.set == 1}{$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']} через запятую{else}{$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['r']}{/if}" />
				</div>
				<div class="apply-object action-button w1" title="Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['i']}{if $property.set == 1}ы{/if}" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
					<i class="icon-prop-apply"></i>
				</div>
			</div>
		{/if}

	{else}
		
		{?$prop_obj = !empty($entity['properties'][$property.key]['complete_value']) ? $entity['properties'][$property.key]['complete_value'] : null}
		{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
		{?$prop_val = !empty($prop_val_id) ? $entity['properties'][$property.key]['value'] : null}

		{if !empty($objects)}
			
			{if $property.values.edit_mode == 'list'}
				{?$title = ''}
				<div class="origin a-hidden row m-fullwidth">
					<div class="drag-drop w05"></div>
					<div class="w105">
						<select class="input-values" name="{$property.key}">
							<option value="" selected>Выберите...</option>
							{if !empty($entities_list)}
								{foreach from=$entities_list item=variant}
									<option value="{$variant.id}">{$variant.variant_title}</option>
								{/foreach}
							{/if}
						</select>
					</div>
					<div class="delete-object action-button w1">
						<i class="icon-prop-delete"></i>
					</div>
				</div>
			{/if}
			{if !empty($prop_val_id)}
				{foreach from=$prop_val_id item=val_id key=val_i}
					{?$obj_id = $prop_val[$val_i]}
					{?$object = $prop_obj[$obj_id]}
					{if !empty($object)}
						<div class="row object-prop row-cont m-fullwidth">
							{if $property.values.edit_mode == 'list'}
								<div class="drag-drop w05"></div>
								<div class="w105">
									<select class="input-values" data-val-id="{$val_id}" name="{$property.key}">
										<option value="" selected>Выберите...</option>
										{if !empty($entities_list)}
											{foreach from=$entities_list item=variant}
												<option value="{$variant.id}"{if $object.id == $variant.id} selected{/if}>{$variant.variant_title}</option>
											{/foreach}
										{/if}
									</select>
								</div>
								<div class="delete-object action-button w1">
									<i class="icon-prop-delete"></i>
								</div>
							{else}
								<div class="w12">
									<div class="row m-fullwidth a-hidden">
										<div class="w11">
											<input type="text" class="input-values" name="{$property.key}" placeholder="ID {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['r']}" value="{$obj_id}" />
										</div>
										<div class="apply-object action-button w1" title="Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['r']}" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
											<i class="icon-prop-apply"></i>
										</div>
									</div>
									<div class="row m-fullwidth">
										<div class="drag-drop w05">
											<input type="hidden" class="input-object" data-val-id="{$val_id}" name="{$property.key}" value="{$obj_id}" />
										</div>
										<div class="w7">
											<a href="{$object->getUrl()}" target="_blank" class="title">{$object.title}</a> <span class="descr">— {$object.id}</span>
										</div>
										<div class="w3">
											{if !empty($object.price)}
												<div>{$object.price|price_format}</div>
											{/if}
										</div>
										<div class="w05"></div>
										<div class="edit-item-object action-button w05" data-object_id="{$obj_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
											<i class="icon-prop-edit"></i>
										</div>
										<div class="delete-object action-button w05">
											<i class="icon-prop-delete"></i>
										</div>
									</div>
								</div>
							{/if}
						</div>
					{/if}
				{/foreach}
			{elseif empty($property.values.edit_mode)}
				{foreach from=$objects item=object}
					<div class="row object-prop row-cont m-fullwidth">
						<div class="w12">
							<div class="row m-fullwidth a-hidden">
								<div class="w11">
									<input type="text" class="input-values" name="{$property.key}" placeholder="ID {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['r']}" value="{$object.id}" />
								</div>
								<div class="apply-object action-button w1" title="Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['1']['r']}" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
									<i class="icon-prop-apply"></i>
								</div>
							</div>
							<div class="row m-fullwidth">
								<div class="drag-drop w05">
									<input type="hidden" class="input-object" name="{$property.key}" value="{$object.id}" />
								</div>
								<div class="w7">
									<a href="{$object->getUrl()}" target="_blank" class="title">{$object.title}</a> <span class="descr">— {$object.id}</span>
								</div>
								<div class="w3">
									{if !empty($object.price)}
										<div>{$object.price|price_format}</div>
									{/if}
								</div>
								<div class="w05"></div>
								<div class="edit-item-object action-button w05" data-object_id="{$object.id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
									<i class="icon-prop-edit"></i>
								</div>
								<div class="delete-object action-button w05">
									<i class="icon-prop-delete"></i>
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			{/if}
			{if $property.values.edit_mode == 'list'}
				<div class="add-row row m-fullwidth">
					<div class="add-item-object add-btn w3">
						<i class="icon-add"></i> <span class="small-descr">Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']}</span>
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
			{else}
				<div class="row add-row row-cont m-fullwidth">
					<div class="w12">

						<div class="row add-row m-fullwidth a-hidden">
							<div class="w11">
								<input type="text" class="input-values" name="{$property.key}" placeholder="ID {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']} через запятую" />
							</div>
							<div class="apply-object action-button w1" title="Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']}" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
								<i class="icon-prop-apply"></i>
							</div>
						</div>

						<div class="add-row row m-fullwidth">
							<div class="edit-item-object add-btn w3">
								<i class="icon-add"></i> <span class="small-descr">Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']}</span>
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
					</div>
				</div>
			{/if}

		{elseif !empty($object)}
			
			{if !empty($catalogs[$object.type_id])}
				{?$key = $catalogs[$object.type_id].key}
			{else}
				{?$key = $object->getType()->getCatalog()}
				{?$key = $key.key}
			{/if}
			{?$obj_id = $object.id}
			<div class="row add-row a-hidden">
				<div class="w11">
					<input type="text" class="input-values" name="{$property.key}" placeholder="ID {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']}" value="{$obj_id}" />
				</div>
				<div class="apply-object action-button w1" title="Добавить {$catalogs[$property['values']['catalog_id']]['word_cases']['v']['2']['r']}" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
					<i class="icon-prop-apply"></i>
				</div>
			</div>
			<div class="row object-prop">
				{if $property.values.edit_mode == 'list'}
					<div class="w12">
						<select class="input-values" name="{$property.key}" data-val-id="{$prop_val_id}">
							<option value="" selected>Выберите...</option>
							{foreach from=$entities_list item=variant}
								<option value="{$variant.id}"{if $object.id == $variant.id} selected{/if}>{$variant.variant_title}</option>
							{/foreach}
						</select>
					</div>
				{else}
					<div class="w1">
						<input type="hidden" class="input-object" name="{$property.key}" value="{$obj_id}" data-val-id="{$prop_val_id}" />
						{if !empty($object.gallery)}
							{?$gallery = $object.gallery}
							{?$cover = $gallery->getCover()}
							{if empty($cover)}
								{?$cover = $gallery->getDefault()}
							{/if}
							{if !empty($cover)}
								<a href="{$cover->getUrl()}" class="row-cover fancybox">
									<img src="{$cover->getUrl(70,70)}" alt="{$object.title}" />
								</a>
							{/if}
						{elseif !empty($object.photo)}
							<a href="{$object.photo->getUrl()}" class="row-cover fancybox">
								<img src="{$object.photo->getUrl(70,70)}" alt="{if !empty($object.surname)}{$object.surname} {/if}{if !empty($object.name)}{$object.name} {/if}{if !empty($object.patronymic)}{$object.patronymic}{/if}" title="{if !empty($object.surname)}{$object.surname} {/if}{if !empty($object.name)}{$object.name} {/if}{if !empty($object.patronymic)}{$object.patronymic}{/if}"/>
							</a>
						{/if}
					</div>
					<div class="w10">
						<a href="{if !empty($key) && $key == 'staff_list'}/catalog-item/edit/?id={$object.id}&tab=options{elseif !empty($key) && $key == 'district'}/catalog-item/edit/?id={$object.id}&tab=options{else}{$object->getUrl()}{/if}" target="_blank">
							{if !empty($object.title)}
								{$object.title}
							{elseif !empty($object.surname) || !empty($object.name) || !empty($object.patronymic)}
								{if !empty($object.surname)}{$object.surname} {/if}{if !empty($object.name)}{$object.name} {/if}{if !empty($object.patronymic)}{$object.patronymic}{/if}
							{/if}
						</a> <span class="descr">— {$object.id}</span>
						{if !empty($object.price)}
							<div>{$object.price|price_format}</div>
						{/if}
					</div>
					<div class="edit-item-object action-button w05" data-object_id="{$obj_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
						<i class="icon-prop-edit"></i>
					</div>

					<div class="delete-object action-button w05">
						<i class="icon-prop-delete"></i>
					</div>
				{/if}
			</div>
		{/if}

	{/if}
</div>