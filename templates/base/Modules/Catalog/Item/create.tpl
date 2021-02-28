{?$includeCss.item_edit = 'Modules/Catalog/Item/edit.css'}
{?$includeJS.item_edit = 'Modules/Catalog/Item/edit.js'}
{?$currentCatalog = $current_type->getCatalog()}
<div class="tabs-cont main-tabs">
	<div class="content-top">
		<h1>Новый {$currentCatalog.nested_in ? $current_type.word_cases['i'][1]['i'] : $currentCatalog.word_cases['i']['1']['i']}</h1>
		<div class="action-panel-cont content-options">
			{include file="Admin/components/actions_panel.tpl"
				buttons = array(
					'back' => '/catalog-type/catalog/?id=' . $catalog_item.type_id
				)}
			{?$child_types = $current_type->getNestedTypes()}
			{?$child_types_data = array()}
			{foreach from=$child_types item=child_type}
				{?$child_types_data[$child_type.key] = array(
					'key' => $child_type.key,
					'url' => '/catalog-item/create/?type_id=' . $catalog_item.type_id,
					'inactive' => 1,
					'text' => $child_type.title,
					'count' => 0
				)}
			{/foreach}
            {?$catalog = $current_type->getCatalog()}
			{?$tabs_list = array(
				'options' => array(
					'url' => '/catalog-item/create/?type_id=' . $catalog_item.type_id,
					'current' => 1,
					'text' => $currentCatalog.nested_in ? $current_type.word_cases['i'][2]['i'] : $currentCatalog.word_cases['i']['1']['i']
				),
				'variants' => ($catalog.only_items == 0)? array(
					'url' => '/catalog-item/create/?type_id=' . $catalog_item.type_id,
					'inactive' => 1,
					'text' => $currentCatalog.word_cases['v']['2']['i'],
					'count' => 0
				) : 0,
				'description' => array(
					'url' => '/catalog-item/create/?type_id=' . $catalog_item.type_id,
					'inactive' => 1,
					'text' => 'Текст',
					'count' => '-'
				),
				'photo' => array(
					'url' => '/catalog-item/create/?type_id=' . $catalog_item.type_id,
					'inactive' => 1,
					'text' => 'Фотогалерея',
					'count' => 0
				)
			)}
			{?$magic_shit = array_splice($tabs_list, 1, 0, $child_types_data)}
			{include file="Admin/components/tabs.tpl" 
				tabs = $tabs_list}
		</div>
	</div>

	<div id="tabs-pages" data-type-id="{$catalog_item.type_id}" class="content-scroll-cont">

		{* Объект *}
		<div id="options" class="tab-page actions-cont m-current">
			<form class="edit_properties_form content-scroll" action="/catalog-item/createJson/" data-type-id="{$catalog_item.type_id}" data-parent_id="{$catalog_item.parent_id}">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
						buttons = array(						
							'save' => 1
						)}
				</div>
				<div class="white-blocks viewport">
					{if !empty($item_variants_properties) && $current_type.only_items == 1}
						{include file="Modules/Catalog/Item/edit_item_properties.tpl" item_variants_properties=$item_variants_properties variant_list=true create=true}
					{/if}
					{if !empty($item_properties)}
						{include file="Modules/Catalog/Item/edit_item_properties.tpl" item_properties=$item_properties variant_list=false create=true}
					{/if}
					<input type="submit" class="a-hidden" />
				</div>
			</form>					
		</div>
	</div>

</div>