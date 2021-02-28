{?$currentCatalog = $current_type->getCatalog()}
{?$no_history = array()}
{* $variant_list - флаг для варианта *}
{if !empty($variant_list) || empty($item_properties)}
	{?$type_properties_run = !empty($item_variants_properties) ? $item_variants_properties : null}
	{?$entity = $catalog_variant}
{else}
	{?$type_properties_run = !empty($item_properties) ? $item_properties : null}
	{?$entity = $catalog_item}
{/if}
{if !empty($show_item_id)}
	<div class="wblock prop-item-cont white-block-row">
		<div class="prop-item w12">
			<div class="prop-title h4">ID {$currentCatalog.nested_in ? $current_type.word_cases['i']['1']['r'] : $currentCatalog.word_cases['i']['1']['r']}</div>
			<div class="field">
				<input type="text" id="postid" value="{$catalog_item.id}" disabled="disabled" />
			</div>
		</div>
	</div>
{/if}
{if $accountType == 'SuperAdmin'}
	<div class="wblock prop-item-cont white-block-row">
		<div class="prop-item w12">
			<div class="prop-title h4">Ключ {$currentCatalog.nested_in ? $current_type.word_cases['i']['1']['r'] : $currentCatalog.word_cases['i']['1']['r']}</div>
			<div class="field">
				<input type="text" value="{$catalog_item.key}" class="not-send input-item-key" />
			</div>
		</div>
	</div>
{/if}
{if !empty($type_properties_run)}
	{?$langs = array()}
	{?$lang_props = array()}
	{foreach from=$segments item=segment}
		{?$langs[$segment['key']] = $segment['id']}
		{if !empty($entity)}
			{?$entity->setSegment($segment.id)}
		{/if}
		{?$lang_props[$segment['key']] = (!empty($entity) ? $entity.properties : null)}
	{/foreach}
	{if !isset($first_group)}{?$first_group = true}{/if}
	{?$type_properties_run_i = 0}
	{?$old_group_id = null}
	{foreach from=$type_properties_run item=property key=prop_key}
		{if $prop_key != 'sort_by_available' && !$property['default_prop']}
			{if $property.fixed != 2 && (!empty($config_props) || !empty($properties_available[$property.id]['available']))}
				{?$prop_field = ''}
				{if !empty($variant_list) || (empty($variant_list) && !empty($property.group_id))}
					{?$has_groups = true}
					{if ($old_group_id != $property.group_id) && empty($variant_list)}
						{if $type_properties_run_i > 0}
								</div>
							</div>
						{/if}
						<div class="props-group slidebox" data-hiddenclass="hidden-group" data-group-id="{$property.group_id}">
							{?$first_group = false}
							{if !empty($property.group_id)}
								<div class="props-group-header">
									<h2 class="slide-header">{$property.group.title}</h2>
								</div>
							{/if}
							<div class="slide-body props-group-list">
							
						{?$old_group_id = $property.group_id}
						{?$type_properties_run_i++}
					{/if}
					{$prop_field|html}
				{/if}
				{include file="Modules/Catalog/Item/property_view.tpl"}
			{/if}
		{/if}
	{/foreach}
	{if empty($variant_list) && !empty($has_groups)}
			</div>
		</div>
		<div class="props-group slidebox" data-hiddenclass="hidden-group" data-group-id="{$property.group_id}">
			<div class="props-group-header">
				<h2 class="slide-header">Параметры групповой сортировки</h2>
			</div>
			<div class="slide-body props-group-list">
				<div class="wblock prop-item-cont white-block-row" style="z-index: 46;">
					<div class="prop-item w12">
						<div class="prop-title h4">Включить в список аренды?</div>
						<div class="field">
							<input type="checkbox" id="is_arda">
						</div>
						<div class="prop-title h4">Комиссия</div>
						<div class="field">
							<input type="text" id="comiss">
						</div>
					</div>
				</div>
			</div>
		</div>
	{/if}
{/if}
<div class="wblock" style="display: none;">
	<input type="hidden" name="last_update" value="{$entity.last_update}" />
</div>