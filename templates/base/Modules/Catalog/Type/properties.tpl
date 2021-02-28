{if !empty($current_type.properties)}
	<div class="white-body sortable" data-url="/catalog-type/properties/" data-cont=".properties-form .properties-list" data-positionattr="position" data-sendattrs="prop_id;type_id" data-newpositionname="position">
		{?$prev_group_id = 0}
		{foreach from=$current_type.properties item=prop}
			{if $prop.fixed != 2 || $accountType == 'SuperAdmin'}
				{if $prop.type_id != $type_id || ($prop.fixed == 1 && $accountType != 'SuperAdmin') || !empty($current_type_unchangeable)}{?$unchangeable = 1}{else}{?$unchangeable = 0}{/if}
				{if $prev_group_id != $prop.group_id}
					{?$prev_group_id = $prop.group_id}
					</div>
					<div class="white-body sortable" data-url="/catalog-type/properties/"  data-cont=".properties-form .properties-list" data-positionattr="position" data-sendattrs="prop_id;type_id" data-newpositionname="position">
						<div class="wblock white-block-row white-header">
							<div class="w12">{$prop.group.title}</div>
						</div>
				{/if}
				<div class="wblock white-block-row{if $unchangeable} unchangeable{/if}" data-position="{$prop.position}" data-prop_id="{$prop.id}" data-type_id="{$current_type.id}">
					<div class="w05 {if $account->isPermission('catalog-type', 'properties')}drag-drop{/if}">
						<input type="hidden" name="prop_id" value="{$prop.id}" />
						{if $account->isPermission('catalog-type', 'properties')}
							<input type="hidden" name="prop_position" value="{$prop.position}" />
						{/if}
					</div>
					<label class="w05">
						<input type="checkbox" name="check[]" class="check-item" value="{$prop.id}"{if $unchangeable || ($accountType != 'SuperAdmin' && !empty($prop.fixed))} disabled{/if} />
					</label>
					{if $unchangeable || !$account->isPermission('catalog-type', 'editProp')}
						<div class="w4">
							<span{if $accountType == 'SuperAdmin'} title="{$prop.key}"{/if}>{$prop.title}</span>
							{if $current_type.id != 1}
								<a href="/catalog-type/{($prop.type_id != $constants.default_type_id ? 'catalog/' : '')}?id={$prop.type_id}&tab=properties" class="title-parent" title="Перейти к типу, определяющему свойство"></a>
								{if $account->isPermission('catalog-type', 'propertyAvailable')}
									<a href="#" class="property_available" title="Обозначить используемость свойства">•••</a>
								{/if}
							{/if}
						</div>
					{else}
						<a href="/catalog-type/editProp/?id={$prop.id}" class="prop-title w4"{if $accountType == 'SuperAdmin'} title="{$prop.key}"{/if}>
							<span>{$prop.title}</span>
						</a>
					{/if}
					<div class="w2">
						<span class="small-descr">
							{if $prop.data_type != 'item' && $prop.data_type != 'variant'}
								{if $prop.data_type == 'gallery'}
									Галерея{*придется так извращаться*}
								{else}
									{$properties_key.data_type[$prop.data_type]}
								{/if}
							{else}
								{?$k = $prop.data_type . $prop_data_type_separator . ($prop.values.catalog_id == 'catalog' ? 2 : $prop.values.catalog_id)}
								{if !empty($properties_key.data_type[$k])}{$properties_key.data_type[$k]}{else}{$k}{/if}
							{/if}
						</span>
					</div>
					<div class="w5">
						<i class="property-icon flag{if $prop.major === null} icon-property_state_for_analogs m-inactive{else} icon-property_state_for_analogs_fill{/if}" title="{if !$prop.major === null}не {/if}главный параметр"></i>
						<i class="property-icon required{if !$prop.necessary} icon-property_state_mandatory m-inactive{else} icon-property_state_mandatory_fill{/if}" title="{if !$prop.necessary}не {/if}обязательное"></i>
						<i class="property-icon unique{if !$prop.unique} icon-property_state_the_only m-inactive{else} icon-property_state_the_only_fill{/if}" title="{if !$prop.unique}не {/if}уникальное"></i>
						<i class="property-icon marker{if !$prop.segment} icon-property_state_segmentable m-inactive{else} icon-property_state_segmentable_fill{/if}" title="{if !$prop.segment}не {/if}сегментированное"></i>
						<i class="property-icon multiple{if !$prop.multiple} icon-property_state_splittable m-inactive{else} icon-property_state_splittable_fill{/if}" title="{if !$prop.multiple}не {/if}расщепляемое"></i>
						<i class="property-icon search{if $prop.search_type == 'none'} icon-property_state_searchable m-inactive{else} icon-property_state_searchable_fill{/if}" 
						   title="{($prop.search_type == 'none')? 'Не участвует в поиске' : $properties_key.search_type[$prop.search_type]}"></i>
						{if $accountType == 'SuperAdmin'}
							<label class="descr">
								{if $prop.type_id == $current_type.id}
									<span class="fix_prop_button a-link">
										{if $prop.fixed === '0'}no
										{elseif $prop.fixed === '1'}fix
										{elseif $prop.fixed === '2'}hide
										{else}lock{/if}
									</span>
								{/if}
							</label>
						{/if}
					</div>
				</div>
			{/if}
		{/foreach}
	</div>
{else}
	<div class="wblock white-block-row">
		<div class="w12">Свойства не заданы</div>
	</div>
{/if}