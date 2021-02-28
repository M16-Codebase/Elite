<div class="wblock prop-item-cont white-block-row lang-props" data-prop-key="{$property.key}" data-prop-id="{$property.id}"{if !empty($property.context)} data-context="{$property.context}"{/if}>
	<div class="prop-item w12
		{if $property.data_type == 'flag' || ($property.data_type == 'enum' && $property.set == 1)} m-cbx
		{elseif $property.data_type == 'enum'} m-select
		{elseif $property->instanceofDataType('diapason')} m-range m-combo combo-cont
		{elseif !empty($property.mask) && $property.mask != ldelim . "!" . rdelim} m-unit m-combo combo-cont
		{else} m-input{/if}{if $property.set == 1} m-multi{/if}
		{if $property.necessary != 0} m-req{/if}{if $property.unique != 0} m-unique{/if}
	">
		<div class="prop-title h4">{!empty($property.title) ? $property.title : $property.key}{if !empty($property.description)} {include file="Admin/components/tip.tpl" content=$property.description}{/if}</div>
		
		{* Флаг *}
		
		{if $property.data_type == 'flag'}
			{if !$property.necessary}
				<label class="field a-block">
					<div class="row">
						{foreach from = $segments item=$s}
							{if $s.key == $constants.segment_default_key}
								<div class="lang-col w6">
									<input type="radio" name="{$property.key}" value=""{if !empty($prop_val_id)} data-val-id="{$prop_val_id}"{/if}{if empty($prop_val_id)} checked{/if} />
									<span class="a-inline-block">Не указано</span>
								</div>
							{else}
								<div class="lang-col {$s.key}-col w6">
									<span class="{$s.key}-var a-hidden">No value</span>
								</div>
							{/if}
						{/foreach}
					</div>
				</label>
			{/if}
			{foreach from=$property.values item=enum_val key=enum_id}
				{if !isset($properties_available[$property.id]['ids']) || !empty($properties_available[$property.id]['ids'][$enum_id])}
					<label class="field a-block">
						<div class="row">
							{foreach from = $segments item=$s}
								{if $s.key == $constants.segment_default_key}
									<div class="lang-col w6">
										<input type="radio" name="{$property.key}" value="{($enum_id == 'yes')? 1 : 0}"{if !empty($prop_val_id)} data-val-id="{$prop_val_id}"{/if}{if ($enum_id == 'yes' && $prop_val == 1) || ($enum_id == 'no' && $prop_val === 0)} checked{/if} />
										<span class="a-inline-block">{if !empty($enum_val[$s.id])}{$enum_val[$s.id]}{else}{$enum_val}{/if}</span>
									</div>
								{else}
									<div class="lang-col {$s.key}-col w6">
										<span class="{$s.key}-var a-hidden">
											{if !empty($enum_val[$s.id])}{$enum_val[$s.id]}{else}{$enum_val}{/if}
										</span>
									</div>
								{/if}
							{/foreach}
						</div>
					</label>
				{/if}
			{/foreach}
			
		
		{* Чекбоксы *}
		
		{elseif $property.data_type == 'enum' &&  $property.set == 1}
			{foreach from=$property.values item=enum_val key=enum_id}
				{?$emum_val_id = $prop_val ? array_search($enum_id, $prop_val) : null}
				{if !isset($properties_available[$property.id]['ids']) || !empty($properties_available[$property.id]['ids'][$enum_id])}
					<label class="field a-block">
						<div class="row">
							{foreach from = $segments item=$s}
								{if $s.key == $constants.segment_default_key}
									<div class="lang-col w6">
										<input type="checkbox" name="{$property.key}" value="{$enum_id}"{if !empty($emum_val_id)} data-val-id="{$emum_val_id}"{/if}{if !empty($prop_val[$emum_val_id])} checked{/if} />
										<span class="a-inline-block">
											{if !empty($enum_val.value)}{$enum_val.value}{else}{$enum_val}{/if}
										</span>
									</div>
								{else}
									<div class="lang-col {$s.key}-col w6">
										<span class="{$s.key}-var a-hidden">
											{!empty($property['segment_enum'][$enum_id][$s.id]) ? $property['segment_enum'][$enum_id][$s.id] : 'No value'}
										</span>
									</div>
								{/if}
							{/foreach}
								
						</div>
					</label>
				{/if}
			{/foreach}


		{* Селекты *}

		{elseif $property.data_type == 'enum'}
			<div class="field">
				<div class="row">
					{foreach from = $segments item=$s}
						{if $s.key == $constants.segment_default_key}
							<div class="lang-col w6">
								<select name="{$property.key}"{if $prop_val_id} data-val-id="{$prop_val_id}"{/if}>
									{if $property.data_type == 'enum' && ($property.necessary != 1 || empty($prop_val))}
										<option value=""{if empty($prop_val)} select{/if}>Выберите...</option>
									{/if}
									{if !empty($property.values)}
										{if $property.data_type == 'enum'}
											{foreach from=$property.values item=enum_val key=enum_id}
												{if !isset($properties_available[$property.id]['ids']) || !empty($properties_available[$property.id]['ids'][$enum_id])}
													<option value="{$enum_id}"{if $prop_val == $enum_id} selected{/if}>
														{$enum_val.value}
													</option>
												{/if}
											{/foreach}
										{else}
											<option value="0"{if $prop_val == 0} selected{/if}>Нет</option>
											<option value="1"{if $prop_val == 1} selected{/if}>Да</option>
										{/if}
									{/if}
									{if !empty($add_enum_val[$property.key])}
										<option value="0" data-add="1">Добавить</option>
									{/if}
								</select>
							</div>
						{else}
							<div class="lang-col {$s.key}-col w6">
								{if !empty($property.values)}
									{if $property.data_type == 'enum'}
										{foreach from=$property.values item=enum_val key=enum_id}
											<div class="{$s.key}-var opt-{$enum_id} a-hidden">{!empty($property['segment_enum'][$enum_id][$s.id]) ? $property['segment_enum'][$enum_id][$s.id] : 'No value'}</div>
										{/foreach}
									{else}
										<div class="{$s.key}-var opt-0 a-hidden">No</div>
										<div class="{$s.key}-var opt-1 a-hidden">Yes</div>
									{/if}
								{/if}
							</div>
						{/if}
					{/foreach}
				</div>
			</div>

			
		{* Диапазоны *}
		
        {elseif $property->instanceofDataType('diapason')}
			<div class="field">
				<div class="row{if $property.data_type == 'diapasonDate'} date-range{/if}">
					{foreach from = $segments item=$s}
						{if $s.key == $constants.segment_default_key}
							<div class="lang-col w6">
								{capture assign=prop_range_min}
									<input type="text" class="short{if $property.data_type == 'diapasonDate'} datepicker{/if}" name="{$property.key}_min" data-val-id="{$entity.properties[$property['key'] . '_min']['val_id']}" value="{if $property.data_type == 'diapasonDate'}{$entity.properties[$property['key'] . '_min']['value']|date_format:'%d.%m.%Y'}{else}{$entity.properties[$property['key'] . '_min']['value']}{/if}" />
								{/capture}
								{capture assign=prop_range_max}
									<input type="text" class="short{if $property.data_type == 'diapasonDate'} datepicker{/if}" name="{$property.key}_max" data-val-id="{$entity.properties[$property['key'] . '_max']['val_id']}" value="{if $property.data_type == 'diapasonDate'}{$entity.properties[$property['key'] . '_max']['value']|date_format:'%d.%m.%Y'}{else}{$entity.properties[$property['key'] . '_max']['value']}{/if}" />
								{/capture}
								{?$prop_range_min = $prop_range_min|trim . " "}
								{?$prop_range_max =  " " . $prop_range_max|trim}
								<div class="range-vals">
									{$property['values'][$s.id]['min_max']|replace:ldelim . "min" . rdelim:$prop_range_min|replace:ldelim . "max" . rdelim:$prop_range_max|html}
									{if !empty($property.segment_data.mask)}
										{$property.segment_data.mask[$s.id]|replace:ldelim . "!" . rdelim:''}
									{/if}
								</div>
							</div>
						{else}
							{?$prop_unit = $property.segment_data.mask[$s.id]}
							{?$delim = ldelim . "!" . rdelim}
							{?$prop_unit = $delim|explode:$prop_unit}
							<div class="lang-col {$s.key}-col w6">
								<div class="min-val range-val a-hidden {$s.key}-var">
									{if !empty($prop_unit[0])}
										{$prop_unit[0] . ' '}
									{/if}
									{$property['values'][$s.id]['min']|replace:ldelim . "min" . rdelim:'<span class="combo-min cmb"></span>'|html}
									{if !empty($prop_unit[1])}
										{' ' . $prop_unit[1]}
									{/if}
								</div>
								<div class="max-val range-val a-hidden {$s.key}-var">
									{if !empty($prop_unit[0])}
										{$prop_unit[0] . ' '}
									{/if}
									{$property['values'][$s.id]['max']|replace:ldelim . "max" . rdelim:'<span class="combo-max cmb"></span>'|html}
									{if !empty($prop_unit[1])}
										{' ' . $prop_unit[1]}
									{/if}
								</div>
								<div class="same-val range-val a-hidden {$s.key}-var">
									{if !empty($prop_unit[0])}
										{$prop_unit[0] . ' '}
									{/if}
									<span class="combo-same cmb"></span>
									{if !empty($prop_unit[1])}
										{' ' . $prop_unit[1]}
									{/if}
								</div>
								<div class="two-val range-val a-hidden {$s.key}-var">
									{if !empty($prop_unit[0])}
										{$prop_unit[0] . ' '}
									{/if}
									{$property['values'][$s.id]['min_max']|replace:ldelim . "min" . rdelim:'<span class="cmb cmb1"></span>'|replace:ldelim . "max" . rdelim:'<span class="cmb cmb2"></span>'|html}
									{if !empty($prop_unit[1])}
										{' ' . $prop_unit[1]}
									{/if}
								</div>
							</div>
						{/if}
					{/foreach}
				</div>
			</div>
			
			
		{* Адрес *}

		{elseif $property.data_type == 'address'}
			<div class="field m-address">
				<div class="row">
					<div class="w12">
						<input type="text" name="{$property.key}" class="address-input"{if $prop_val_id} data-val-id="{$prop_val_id}"{/if}{if !empty($prop_val)} value="{$prop_val}"{/if} />
					</div>
				</div>
				<div class="row">
					<div class="w11">
						<input type="text" name="{$property.key}_coords" class="coords-input" placeholder="Координаты на карте" 
							{if !empty($entity.properties[$property['key'] . '_coords']['val_id'])}data-val-id="{$entity.properties[$property['key'] . '_coords']['val_id']}"{/if} 
							{if !empty($entity.properties[$property['key'] . '_coords']['value'])}value="{$entity.properties[$property['key'] . '_coords']['value']}"{/if} />
					</div>
					<div class="set-marker action-button w1" title="Указать на карте">
						<i class="icon-property_state_segmentable"></i>
					</div>
				</div>
			</div>
		
		
		{* Координаты *}

		{elseif $property.data_type == 'coords'}
			<div class="field m-address">
				<div class="row">
					<div class="w11">
						<input type="text" name="{$property.key}" class="coords-input"{if !empty($prop_val_id)} data-val-id="{$prop_val_id}"{/if}{if !empty($prop_val)} value="{$prop_val}"{/if} />
					</div>
					<div class="set-marker action-button w1" title="Указать на карте">
						<i class="icon-property_state_segmentable"></i>
					</div>
				</div>
			</div>
		
		
		{* Полигоны *}
		
		{elseif $property.key == 'sheme_coords'}
			{?$parent_item = $catalog_item->getParent()}
			{if !empty($parent_item)}
				{if $property.set == 1}
					<div class="field sortable m-poly" data-notsend="1">
						<div class="multi-item origin row a-hidden">
							{if !empty($parent_item.properties.sheme_get.complete_value)}
								<div class="w11">
									<input type="text" name="{$property.key}" class="coords-input" />
								</div>
								<div class="set-poly action-button w05" title="Указать на схеме" data-img="{$parent_item.properties.sheme_get.complete_value->getUrl()}">
									<i class="icon-polygon"></i>
								</div>
								<div class="delete-item action-button w05" title="Удалить" data-text="{!empty($property.title)? $property.title : $property.key}">
									<i class="icon-prop-delete"></i>
								</div>
							{else}
								<div class="w12 small-descr">
									<span class="small-descr">Схема не загружена</span>
								</div>
							{/if}
						</div>
				{else}
					<div class="field m-poly">
						<div class="row">
							{if !empty($parent_item.properties.sheme_get.complete_value)}
								<div class="w11">
									<input type="text" name="{$property.key}" class="coords-input"{if !empty($prop_val_id)} data-val-id="{$prop_val_id}"{/if}{if !empty($prop_val)} value="{$prop_val}"{/if} />
								</div>
								<div class="set-poly action-button w1" title="Указать на схеме" data-img="{$parent_item.properties.sheme_get.complete_value->getUrl()}">
									<i class="icon-polygon"></i>
								</div>
							{else}
								<div class="w12 small-descr">
									<span class="small-descr">Схема не загружена</span>
								</div>
							{/if}
						</div>
					</div>
				{/if}
				{if $property.set == 1}
					{if !empty($parent_item.properties.sheme_get.complete_value)}
						{if !empty($prop_val_id)}
							{foreach from=$prop_val_id item=val_id key=val_i}								
								<div class="multi-item row">
									<div class="w11">
										<input type="text" name="{$property.key}" class="coords-input"{if !empty($val_id)} data-val-id="{$val_id}"{/if}{if !empty($prop_val[$val_id])} value="{$prop_val[$val_id]}"{/if} />
									</div>
									<div class="set-poly action-button w05" title="Указать на схеме" data-img="{$parent_item.properties.sheme_get.complete_value->getUrl()}">
										<i class="icon-polygon"></i>
									</div>
									<div class="delete-item action-button w05" title="Удалить" data-text="{!empty($property.title)? $property.title : $property.key}">
										<i class="icon-prop-delete"></i>
									</div>
								</div>
							{/foreach}
						{/if}
						<div class="add-row row m-fullwidth unchangeable">
							<div class="add-button add-btn w3">
								<i class="icon-add"></i> <span class="small-descr">Добавить</span>
							</div>
							<div class="w8"></div>
							<div class="w1">
								<div class="prop-menu dropdown">
									<div class="dropdown-toggle">
										<i class="icon-prop-more"></i>
									</div>
									<ul class="dropdown-menu a-hidden">
										<li><a href="#" class="delete-all">Удалить все</a></li>
									</ul>
								</div>
							</div>
						</div>
					{else}
						<div class="row">
							<div class="w12 small-descr">
								<span class="small-descr">Схема не загружена</span>
							</div>
						</div>
					{/if}
						</div>
				{/if}
			{/if}
		
		
		{* Дата, цвет *}

		{elseif $property.data_type == 'date' || $property.data_type == 'color'}	
			{if $property.set == 1}
				<div class="field sortable" data-notsend='1'>
					<div class="multi-item origin row a-hidden" data-add-class="{if $property.data_type == 'date'}datepicker{elseif $property.data_type == 'color'}colorpicker{/if}">
						<div class="drag-drop w05"></div>
			{else}
				<div class="field">
					<div class="row">
			{/if}
			<div class="{if $property.set == 1}w10{else}w12{/if}{if  $property.data_type == 'color'} colorpicker-block{/if}">
				<input type="text" name="{$property.key}"
					{if $property.set != 1} value="{if $property.data_type == 'date'}{$prop_val|date_format:'%d.%m.%Y'}{else}{$prop_val}{/if}"{else}value=''{/if}
					{if $property.set != 1} data-val-id="{$prop_val_id}"{/if}
					{if $property.read_only == 1} disabled{/if}
					{if $property.set != 1}
						{if $property.data_type == 'date'} class="datepicker"{/if}
						{if $property.data_type == 'color'} class="colorpicker"{/if}
					{/if}
					/>
				{if $property.data_type == 'color'}<input type="text" class="m-small colorpicker-input">{/if}
			</div>
			{if $property.set == 1}
						<div class="w05"></div>
						<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
							<i class="icon-prop-delete"></i>
						</div>
					</div>
				
				{if !empty($prop_val_id)}
					{foreach from=$prop_val_id item=val_id key=val_i}								
						<div class="multi-item row">
							<div class="drag-drop w05"></div>
							<div class="w10{if  $property.data_type == 'color'} colorpicker-block{/if}">
								<input type="text" name="{$property.key}" class="title{if $property.data_type == 'date'} datepicker{elseif $property.data_type == 'color'} colorpicker{/if}"
									{if !empty($prop_val[$val_id])} value="{if $property.data_type == 'date'}{$prop_val[$val_id]|date_format:'%d.%m.%Y'}{else}{$prop_val[$val_id]}{/if}"{/if}
									{if !empty($val_id)} data-val-id="{$val_id}"{/if}
									{if $property.read_only == 1} disabled{/if} />
								{if $property.data_type == 'color'}<input type="text" class="m-small colorpicker-input">{/if}
							</div>
							<div class="w05"></div>
							<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
								<i class="icon-prop-delete"></i>
							</div>
						</div>
					{/foreach}
				{/if}
					<div class="add-row row m-fullwidth unchangeable">
						<div class="add-button add-btn w3">
							<i class="icon-add"></i> <span class="small-descr">Добавить</span>
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
			{else}
					</div>
				</div>
			{/if}
			
			
		{* Инпуты с маской *}

		{elseif !empty($property.mask) && $property.mask != ldelim . "!" . rdelim}
			{if $property.set == 1}
				<div class="field sortable" data-notsend="1">
					<div class="multi-item combo-cont origin row a-hidden">
						<div class="drag-drop w05"></div>
			{else}
				<div class="field">
					<div class="row">
			{/if}
				{foreach from = $segments item=$s}
					{if $s.key == $constants.segment_default_key}
						<div class="lang-col{if $property.set == 1}{if $s.key == $constants.segment_default_key} w55{else} w5{/if}{else} w6{/if}">
							{?$input_string = '<input type="text" '}
							{?$input_string .= 'name="' . $property.key . '" '}
							{?$input_string .= 'data-key="' . $property.key . '" '}
							{if $property.set != 1 && !empty($prop_val_id)}
								{?$input_string .= 'value="' . $lang_values[$s.key][$property.key]['value'] . '" '}
								{?$input_string .= 'data-val-id="' . $prop_val_id . '" '}
							{/if}
							{if $property.data_type == 'int'}{?$input_string .= 'data-mask="?9999999999" '}{/if}
							{?$input_string .= ' data-segment="'.$s.id.'"/>'}
							{?$prop_unit = $property.segment_data.mask[$s.id]|replace:ldelim . "!" . rdelim:''}
							{?$prop_string = $property.segment_data.mask[$s.id]|replace:$prop_unit:('<span class="prop-unit">' . $prop_unit . '</span>')}
							{($prop_string|replace:ldelim . "!" . rdelim:$input_string)|html}
						</div>
					{else}
						<div class="combo-{$property.key} lang-col {$s.key}-col{if $property.set == 1} w5{else} w6{/if}">
							{?$prop_unit = $property.segment_data.mask[$s.id]}
							{?$delim = ldelim . "!" . rdelim}
							{?$prop_unit = $delim|explode:$prop_unit}
							{if !empty($prop_unit[0])}
								{$prop_unit[0] . ' '}
							{/if}
							<span class="{$s.key}-var">
							</span>
							{if !empty($prop_unit[1])}
								{' ' . $prop_unit[1]}
							{/if}
						</div>
					{/if}
				{/foreach}
				{if $property.set == 1}
						<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
							<i class="icon-prop-delete"></i>
						</div>
					</div>
					{?$prop_val_ids_by_pos = array()}
					{foreach from=$segments item=segment}
						{foreach from=$lang_values[$segment.key][$property.key]['val_id'] item=$val_id}
							{?$prop_val_ids_by_pos[$lang_values[$segment.key][$property.key]['position'][$val_id]][$segment.key] = $val_id}
						{/foreach}
					{/foreach}
					{if !empty($prop_val_ids_by_pos)}
						{foreach from=$prop_val_ids_by_pos item=val_ids key=pos}
							<div class="multi-item combo-cont row">
								<div class="drag-drop w05"></div>
								{foreach from = $segments item=$s}
									{?$segment_field_val_id = $prop_val_ids_by_pos[$pos][$s.key]}
									{if $s.key == $constants.segment_default_key}
										<div class="lang-col {if $property.set == 1} w5{else} w6{/if}">
											{?$input_string = '<input type="text" '}
											{?$input_string .= 'name="' . $property.key . '" '}
											{?$input_string .= 'data-key="' . $property.key . '" '}
											{if !empty($prop_val[$val_id])}
												{?$input_string .= 'value="' . $lang_values[$s.key][$property.key]['value'][$segment_field_val_id] . '" '}
												{?$input_string .= 'data-val-id="' . $segment_field_val_id. '" '}
											{/if}
											{if $property.data_type == 'int'}{?$input_string .= 'data-mask="?9999999999" '}{/if}
											{?$input_string .= ' data-segment="'.$s.id.'"/>'}
											{?$prop_unit = $property.segment_data.mask[$s.id]|replace:ldelim . "!" . rdelim:''}
											{?$prop_string = $property.segment_data.mask[$s.id]|replace:$prop_unit:('<span class="prop-unit">' . $prop_unit . '</span>')}
											{($prop_string|replace:ldelim . "!" . rdelim:$input_string)|html}
										</div>
									{else}
										<div class="combo-{$property.key} lang-col {$s.key}-col{if $property.set == 1} w5{else} w6{/if}">
											<span class="{$s.key}-var">
											</span>
											{$property.segment_data.mask[$s.id]|replace:ldelim . "!" . rdelim:''|html}
										</div>
										<div class="combo-{$property.key} lang-col {$s.key}-col{if $property.set == 1} w5{else} w6{/if}">
											{?$prop_unit = $property.segment_data.mask[$s.id]}
											{?$delim = ldelim . "!" . rdelim}
											{?$prop_unit = $delim|explode:$prop_unit}
											{if !empty($prop_unit[0])}
												{$prop_unit[0] . ' '}
											{/if}
											<span class="{$s.key}-var">
											</span>
											{if !empty($prop_unit[1])}
												{' ' . $prop_unit[1]}
											{/if}
										</div>
									{/if}
								{/foreach}
								<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
									<i class="icon-prop-delete"></i>
								</div>
							</div>
						{/foreach}
					{/if}
						<div class="add-row row m-fullwidth unchangeable">
							<div class="add-button add-btn w3">
							<i class="icon-add"></i> <span class="small-descr">Добавить</span>
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
				{else}
						</div>
					</div>
				{/if}

			
		{* Тексты *}

		{elseif $property.data_type == 'text'}
			{if $property.set == 1}
				<div class="field sortable">
					<div class="multi-item origin row a-hidden">
						<div class="drag-drop w05"></div>
			{/if}
						<div class="{if $property.set == 1}w10{else}field{/if}">
							<textarea name="{$property.key}" {if $property.set != 1} data-val-id="{$prop_val_id}"{/if}>
								{if $property.set != 1}
									{$prop_val}
								{/if}
							</textarea>
						</div>
			{if $property.set == 1}
				<div class="w05"></div>
					<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
						<i class="icon-prop-delete"></i>
					</div>	
				</div>
				{if !empty($prop_val_id)}
					{foreach from=$prop_val_id item=val_id key=val_i}								
						<div class="multi-item row">
							<div class="drag-drop w05"></div>
							<div class="field w10">
								<textarea name="{$property.key}"{if !empty($val_id)} data-val-id="{$val_id}"{/if}>
									{if !empty($prop_val[$val_id])}
										{$prop_val[$val_id]}
									{/if}
								</textarea>
							</div>
							<div class="w05"></div>
							<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
								<i class="icon-prop-delete"></i>
							</div>	
						</div>	
					{/foreach}
				{/if}
					<div class="add-row row m-fullwidth unchangeable">
						<div class="add-button add-btn w3">
						<i class="icon-add"></i> <span class="small-descr">Добавить</span>
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
			{/if}

			
		{* Объекты *}

		{elseif $property->instanceofDataType('entity')}
			<div class="object-prop-preloader" data-entity_id="{if !empty($editing_variant_id)}{$editing_variant_id}{elseif !empty($catalog_item) && !empty($catalog_item.id)}{$catalog_item.id}{/if}" data-property_id="{$property.id}" data-segment_id="0"></div>
		
			
		{* Остальное *}

		{else}
			{if $property.set == 1}
				<div class="field sortable" data-notsend='1'>
					<div class="multi-item origin row a-hidden" data-add-class="{if $property.data_type == 'date'}datepicker{elseif $property.data_type == 'color'}colorpicker{/if}">
						<div class="drag-drop w05"></div>
			{/if}
			<div class="{if $property.set == 1}w10{else}field{/if}{if $property.data_type == 'color'} colorpicker-block{/if}">
				<input type="text" name="{$property.key}"
					{if $property.set != 1} value="{if $property.data_type == 'date'}{$prop_val|date_format:'%d.%m.%Y'}{else}{$prop_val}{/if}"{else}value=''{/if}
					{if $property.set != 1} data-val-id="{$prop_val_id}"{/if}
					{if $property.read_only == 1} disabled{/if}
					{if $property.data_type == 'int'} data-mask="?9999999999"{/if}
					{if $property.set != 1}
						{if $property.data_type == 'date'} class="datepicker"{/if}
						{if $property.data_type == 'color'} class="colorpicker"{/if}
					{/if}
					/>
				{if $property.data_type == 'color'}<input type="text" class="m-small colorpicker-input">{/if}
			</div>
			{if $property.set == 1}
						<div class="w05"></div>
						<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
							<i class="icon-prop-delete"></i>
						</div>
					</div>
				{if !empty($prop_val_id)}
					{foreach from=$prop_val_id item=val_id key=val_i}								
						<div class="multi-item row">
							<div class="drag-drop w05"></div>
							<div class="field w10{if  $property.data_type == 'color'} colorpicker-block{/if}">
								<input type="text" name="{$property.key}" class="title{if $property.data_type == 'date'} datepicker{elseif $property.data_type == 'color'} colorpicker{/if}"
									{if !empty($prop_val[$val_id])} value="{if $property.data_type == 'date'}{$prop_val[$val_id]|date_format:'%d.%m.%Y'}{else}{$prop_val[$val_id]}{/if}"{/if}
									{if !empty($val_id)} data-val-id="{$val_id}"{/if}
									{if $property.read_only == 1} disabled{/if}
									{if $property.data_type == 'int'} data-mask="?9999999999"{/if} />
								{if $property.data_type == 'color'}<input type="text" class="m-small colorpicker-input">{/if}
							</div>
							<div class="w05"></div>
							<div class="delete-item action-button w1" title="Удалить" data-text='{!empty($property.title) ? $property.title : $property.key}'>
								<i class="icon-prop-delete"></i>
							</div>
						</div>
					{/foreach}
				{/if}
					<div class="add-row row m-fullwidth unchangeable">
						<div class="add-button add-btn w3">
							<i class="icon-add"></i> <span class="small-descr">Добавить</span>
						</div>
						<div class="w8"></div>
						<div class="w1">
							<div class="prop-menu dropdown">
								<div class="dropdown-toggle">
									<i class="icon-prop-more"></i>
								</div>
								<ul class="dropdown-menu a-hidden">
									<li><a href="#" class="delete-all">Удалить все</a></li>
									{if $property.data_type == 'int' || $property.data_type == 'float'}
										<li><a href="#" class="sort-asc">Отсортировать по возрастанию</a></li>
										<li><a href="#" class="sort-descr">Отсортировать по убыванию</a></li>
									{else}
										<li><a href="#" class="sort-alph">Отсортировать по алфавиту</a></li>
									{/if}
								</ul>
							</div>
						</div>
					</div>
				</div>
			{/if}
		{/if}
	</div>
</div>