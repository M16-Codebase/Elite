{?$currentCatalog = $current_type->getCatalog()}

<form action="/catalog-item/moveVariant/" class="move-variants-form">
	<div class="popup-preloader"></div>
	<input type="hidden" name="item_type_id" value="{$current_type.id}" />
	<div class="content-top">
		<h1>Изменение порядка{if !empty($currentCatalog.word_cases['v']['2']['r'])} {$currentCatalog.word_cases['v']['2']['r']}{/if}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена')
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="selected-items a-hidden"></div>
	<div class="content-scroll">
		<div class="variants-list white-blocks sortable viewport" data-url="/catalog-item/moveVariant/" data-cont=".edit-content.m-current" data-sendattrs="variant_id" data-newpositionname="position">
			{foreach from=$catalog_item_variants item=$var}
				<div class="wblock white-block-row" data-variant_id="{$var.id}" data-position="{$var.position}">
					<div class="w05 drag-drop"></div>
					<div class="w11">
						{if !empty($var.code)}{$var.code}{/if}
						{if !empty($var.variant_title)} {$var.variant_title}{else} No title{/if}
					</div>
					<div class="w05"></div>
				</div>
			{/foreach}
		</div>
	</div>
</form>