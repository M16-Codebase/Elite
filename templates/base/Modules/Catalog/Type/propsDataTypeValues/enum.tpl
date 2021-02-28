{if $constants.segment_mode == 'lang'}
	<div class="white-block-row">
		<div class="w3">Значения для сегментов:</div>
		<div class="w9 prop-item m-multi m-input">
			<div class="sortable being-values" data-notsend="1">
				<div class="multi-item row origin a-hidden">
					<div class="drag-drop w05"></div>
					{foreach from = $segments item=$s}
						<div class="w4">
							<input class="input-val" name="values[values][{$s.id}][]"  type="text" value=""{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
						</div>
					{/foreach}
					<div class="w2{if $accountType != 'SuperAdmin'} a-hidden{/if}">
						<input name="values[keys][]" class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value=""{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
					</div>
					<div class="delete-item action-button w1 remove-enum-value" data-enum_id="" title="Удалить">
						<i class="icon-prop-delete"></i>
					</div>
				</div>
				{if ($property.data_type == 'enum') && !empty($property.values) && is_array($property.values)}
					{foreach from=$property.values item=enum_value key=enum_id}
						<div class="multi-item row one-value">
							<div class="drag-drop w05"></div>
							{foreach from = $segments item=$s}
								<div class="w4">
									<input name="values[values][{$s.id}][{$enum_id}]" class="input-val" type="text" value="{if !empty($property.segment_enum[$enum_id][$s.id])}{$property.segment_enum[$enum_id][$s.id]}{/if}"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
								</div>
							{/foreach}
							<div class="w2{if $accountType != 'SuperAdmin'} a-hidden{/if}">
								<input name="values[keys][{$enum_id}]" class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value="{$enum_value.key}"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
							</div>
							<div class="delete-item action-button w1 remove-enum-value" data-enum_id="{$enum_id}" title="Удалить">
								<i class="icon-prop-delete"></i>
							</div>
						</div>
					{/foreach}
				{/if}
				{if !$unchangeable || !in_array('values', $unchangeableParamsByProps)}
					<div class="multi-item row add-form unchangeable">
						<div class="w05"></div>
						{foreach from = $segments item=$s}
							<div class="w4">
								<input class="input-val" type="text" value="" placeholder="Значение ({$s.key})"/>
							</div>
						{/foreach} 
						<div class="w2{if $accountType != 'SuperAdmin'} a-hidden{/if}">
							<input class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value="" placeholder="Ключ" />
						</div>
						<div class="table-btn add a-hidden w1 action-button" title="Добавить значение"><i class="icon-add"></i></div>
					</div>
					<div class="enum-sort">
						<span class="enum-alph-sort small-descr a-link">Сортировать по алфавиту</span>
					</div>
				{/if}
			</div>
		</div>
	</div>
{else}
	<div class="white-block-row">
		<div class="w3">Значения:</div>
		<div class="w9 prop-item m-multi m-input">
			<div class="sortable being-values" data-notsend="1">
				{if ($property.data_type == 'enum') && is_array($property.values) && !empty($property.values)}
					<div class="multi-item row origin a-hidden">
						<div class="drag-drop w05"></div>
						<div class="w6">
							<input class="input-val" name="values[values][]"  type="text" value=""{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
						</div>
						<div class="w45{if $accountType != 'SuperAdmin'} a-hidden{/if}">
							<input name="values[keys][]" class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value=""{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
						</div>
						<div class="delete-item action-button w1 remove-enum-value" data-enum_id="" title="Удалить">
							<i class="icon-prop-delete"></i>
						</div>
					</div>
					{if !empty($property.values) && is_array($property.values)}
						{foreach from=$property.values item=enum_value key=enum_id}
							<div class="multi-item row one-value">
								<div class="drag-drop w05"></div>
								<div class="w6">
									<input name="values[values][{$enum_id}]" class="input-val" type="text" value="{if !empty($enum_value.value)}{$enum_value.value}{/if}"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
								</div>
								<div class="w45{if $accountType != 'SuperAdmin'} a-hidden{/if}">
									<input name="values[keys][{$enum_id}]" class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value="{$enum_value.key}"{if $unchangeable && in_array('values', $unchangeableParamsByProps)} disabled{/if} />
								</div>
								<div class="delete-item action-button w1 remove-enum-value" data-enum_id="{$enum_id}" title="Удалить">
									<i class="icon-prop-delete"></i>
								</div>
							</div>
						{/foreach}
					{/if}
					{if !$unchangeable || !in_array('values', $unchangeableParamsByProps)}
						<div class="multi-item row add-form unchangeable">
							<div class="w05"></div>
							<div class="w6">
								<input class="input-val" type="text" value="" placeholder="Значение"/>
							</div>
							<div class="w4{if $accountType != 'SuperAdmin'} a-hidden{/if}">
								<input class="input-key" type="{if $accountType == 'SuperAdmin'}text{else}hidden{/if}" value="" placeholder="Ключ" />
							</div>
							<div class="table-btn add a-hidden w1 action-button" title="Добавить значение"><i class="icon-add"></i></div>
						</div>
						<div class="enum-sort">
							<span class="enum-alph-sort small-descr a-link">Сортировать по алфавиту</span>
						</div>
					{/if}
				{/if}
			</div>
		</div>
	</div>
{/if}