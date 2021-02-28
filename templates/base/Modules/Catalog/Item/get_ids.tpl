{?$currentCatalog = $current_type->getCatalog()}
<form action="/catalog-item/changeFilteredItemsProp/" class="get-ids-form">
	<div class="popup-preloader"></div>
{*	<input type="hidden" name="item_type_id" value="{$current_type.id}" />*}
	<div class="content-top">
		<h1>ID и НН найденных {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['r'] : $currentCatalog.word_cases['i']['2']['r']}</h1>
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
{*	<div class="selected-items a-hidden"></div>*}
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock">
				<div class=" white-block-row">
					<div class="w12">
						<strong>ID</strong>
						<textarea name="ids" class="item-ids" rows="4"></textarea>
					</div>
				</div>
			</div>
			<div class="wblock">
				<div class=" white-block-row">
					<div class="w12">
						<strong>HH</strong>
						<textarea name="ids" class="var-ids" rows="4"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
