<form action="/catalog-type/saveDynamicRule/" class="edit-rule-form">
	<input type="hidden" name="type_id" class="type-input" />
	<input type="hidden" name="rule_id" class="rule-input" />
	<div class="content-top">
		<h1>{if empty($rule_id)}Создание{else}Редактирование{/if} правила</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Создать',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="viewport">
			<div class="white-blocks overview">
				{if !empty($dc_types_by_level)}
					<div class="wblock rule-types">
						<div class="white-block-row">
							<div class="w12"><strong>Принадлежит одному из типов</strong></div>
						</div>
						<div class="white-inner-cont rules-data">
							<div class="white-block-row origin a-hidden">
								<div class="w11">
									<select name="" class="type-select">
										<option value="">Выберите...</option>
										{foreach from=$dc_types_by_level key=type_id item=type}
											<option value="{$type_id}" class="level{$type.level+1}">
												{section loop=$type.level name=level}-{/section}{$type.data.title}
											</option>
										{/foreach}
									</select>
								</div>
								<div class="w1 action-button action-delete">
									<i class="icon-delete" title="Удалить правило"></i>
								</div>
							</div>
							{if !empty($dc_rule.type_id.value)}
								{foreach from=$dc_rule.type_id.value item=rule_type_id}
									<div class="white-block-row">
										<div class="w11">
											<select name="" class="type-select">
												<option value="">Выберите...</option>
												{foreach from=$dc_types_by_level key=type_id item=type}
													<option value="{$type_id}" class="level{$type.level+1}"{if $rule_type_id == $type_id} selected{/if}>
														{section loop=$type.level name=level}-{/section}{$type.data.title}
													</option>
												{/foreach}
											</select>
										</div>
										<div class="w1 action-button action-delete">
											<i class="icon-delete" title="Удалить правило"></i>
										</div>
									</div>
								{/foreach}
							{else}
								<div class="white-block-row">
									<div class="w11">
										<select name="" class="type-select">
											<option value="">Выберите...</option>
											{foreach from=$dc_types_by_level key=type_id item=type}
												<option value="{$type_id}" class="level{$type.level+1}">
													{section loop=$type.level name=level}-{/section}{$type.data.title}
												</option>
											{/foreach}
										</select>
									</div>
									<div class="w1 action-button action-delete">
										<i class="icon-delete" title="Удалить правило"></i>
									</div>
								</div>
							{/if}
							<div class="add-row row">
								<div class="w12">
									<div class="add-button add-btn w3">
										<i class="icon-add"></i> <span class="small-descr">Добавить</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}
				<div class="wblock rule-props">
					<div class="white-block-row">
						<div class="w12"><strong>Содержит свойства</strong></div>
					</div>
					<div class="white-inner-cont rules-data">
						<div class="white-block-row origin a-hidden">
							<div class="w3">
								<select name="" class="prop-select">
									{include file="Modules/Catalog/Type/dynamicRuleProps.tpl" rule_prop_key=null}
								</select>
							</div>
							<div class="w8 rule-prop-opts"></div>
							<div class="w1 action-button action-delete">
								<i class="icon-delete" title="Удалить правило"></i>
							</div>
						</div>
						{if !empty($dc_rule)}
							{foreach from=$dc_rule key=rule_prop_key item=rule_prop}
								{if $rule_prop_key != 'type_id'}
									<div class="white-block-row">
										<div class="w3">
											<select name="" class="prop-select">
												{?$selected_prop = null}
												{include file="Modules/Catalog/Type/dynamicRuleProps.tpl" rule_prop_key=$rule_prop_key}
											</select>
										</div>
										<div class="w8 rule-prop-opts">
											{include file="Modules/Catalog/Type/dynamicRulePropFields.tpl" values_type=$selected_prop.data_type}
										</div>
										<div class="w1 action-button action-delete">
											<i class="icon-delete" title="Удалить правило"></i>
										</div>
									</div>
								{/if}
							{/foreach}
						{else}
							<div class="white-block-row">
								<div class="w3">
									<select name="" class="prop-select">
										{include file="Modules/Catalog/Type/dynamicRuleProps.tpl" rule_prop_key=null}
									</select>
								</div>
								<div class="w8 rule-prop-opts"></div>
								<div class="w1 action-button action-delete">
									<i class="icon-delete" title="Удалить правило"></i>
								</div>
							</div>
						{/if}
						<div class="add-row row">
							<div class="w12">
								<div class="add-button add-btn w3">
									<i class="icon-add"></i> <span class="small-descr">Добавить</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>