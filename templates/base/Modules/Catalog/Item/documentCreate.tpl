<h1>Новый документ</h1>

{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'back' => ('/catalog-item/?id=' . $catalog_item.type_id)		
	)}

<div class="tabs-wrap">
	<ul id="tabs">
           <li><a data-tab="options" href="/catalog-item/create/?type_id={$catalog_item.type_id}" class="m-current">Документ</a></li>
	</ul>
	<div id="tabs-pages" data-type-id="{$catalog_item.type_id}" class="main-content-gray">
		{* Объект *}
		<div id="options" class="tab-page aside-controls m-current">
			<form class="edit_properties_form" action="/catalog-item/createJson/" data-type-id="{$catalog_item.type_id}">
				<div class="aside-panel">
					<div class="aside-float">						
						{include file="Admin/components/actions_panel.tpl"
							buttons = array(						
								'save' => 1
							)}
					</div>
				</div>
				<div>
					{if !empty($item_variants_properties) && $current_type.only_items == 1}
						{include file="Modules/Catalog/Item/Documents/edit_item_properties.tpl" item_variants_properties=$item_variants_properties variant_list=true create=true}
					{/if}
					{if !empty($item_properties)}
						{include file="Modules/Catalog/Item/Documents/edit_item_properties.tpl" item_properties=$item_properties variant_list=false create=true}
					{/if}
					<input type="submit" class="a-hidden" />
				</div>	
			</form>					
		</div>
	</div>
</div>