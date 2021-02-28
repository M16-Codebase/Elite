<div class="wblock white-block-row white-header">
	<div class="w12">Рабочий каталог: <strong>{$dc_catalog.title}</strong></div>
</div>

{if !empty($dc_rules)}
	{foreach from=$dc_rules key=rule_id item=rule}
		<div class="wblock white-block-row" data-id="{$rule_id}">
			 <label class="w05">
				<input type="checkbox" class="check-item" />
			</label>
			<div class="w9">
				{if empty($rule.type_id)}
					<div>Поиск во всех типах каталога по свойствам:</div>
				{/if}
				{foreach from=$rule key=rule_prop_key item=rule_prop_params}
					<div>
						{if $rule_prop_key == 'type_id'}
							{if !empty($rule_prop_params['value'])}
								Поиск в {count($rule.type_id.value)|plural_form:'типе':'типах':'типах':false} 
								{?$dc_type_ids = is_array($rule_prop_params['value']) ? $rule_prop_params['value'] : array($rule_prop_params['value'])}
								{foreach from=$dc_type_ids value=$dc_type_id name=dc_type_ids}
									{if !empty($dc_used_types[$dc_type_id])}
										{if iteration > 1}, {/if}
										<strong>{$dc_used_types[$dc_type_id].title}</strong>
									{/if}
								{/foreach}
								по свойствам:
							{/if}
						{else}
							{if empty($dc_used_props[$rule_prop_key])}
								Поиск по свойству <strong>{$rule_prop_key}</strong> невозможен
							{else}
								<strong>{$dc_used_props[$rule_prop_key].title}</strong>
								<span class="small-descr">
									{if isset($rule_prop_params.min) || isset($rule_prop_params.max)}
										({if isset($rule_prop_params.min)}от {$rule_prop_params.min} {/if}{if isset($rule_prop_params.max)}до {$rule_prop_params.max}{/if})
									{elseif isset($rule_prop_params.value)}
										{if is_array($rule_prop_params.value)}
											({foreach from=$rule_prop_params.value item=prop_val name=rule_prop_params}
												{if iteration > 1}, {/if}
												{if !empty($dc_used_props[$rule_prop_key]['values'][$prop_val]['value'])}
													{$dc_used_props[$rule_prop_key]['values'][$prop_val]['value']}
												{elseif in_array($prop_val, array(1, 0)) && !empty($dc_used_props[$rule_prop_key]['values'][$prop_val == 1 ? 'yes' : 'no'])}
													{$dc_used_props[$rule_prop_key]['values'][$prop_val == 1 ? 'yes' : 'no']}
												{else}
													{$dc_used_props[$rule_prop_key]['values'][$prop_val]}
												{/if}
											{/foreach})
										{else}
											({$rule_prop_params.value})
										{/if}
									{/if}
								</span>
							{/if}
						{/if}
					</div>
				{/foreach}
			</div>
			<div class="w05"></div>
			<div class="w1 action-button action-edit">
				<i class="icon-edit" title="Редактировать правило"></i>
			</div>
			<div class="w1 action-button action-delete m-border">
				<i class="icon-delete" title="Удалить правило"></i>
			</div>
	   </div>
	{/foreach}
{else}
	<div class="wblock white-block-row">
		<div class="w12">Правил еще нет</div>
	</div>
{/if}