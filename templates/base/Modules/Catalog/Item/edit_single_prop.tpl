{?$currentCatalog = $current_type->getCatalog()}
<form action="/catalog-item/changeFilteredItemsProp/" class="edit-single-prop-form">
	<div class="popup-preloader"></div>
	<input type="hidden" name="type_id" value="{$current_type.id}" />
	<div class="content-top">
		<h1>Групповое редактирование свойств</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="selected-items a-hidden"></div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w12">Выбрано {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['r'] : $currentCatalog.word_cases['i']['2']['r']}: <span class="count"></span></div>
			</div>
			<div class="wblock white-block-row white-header">
				<div class="w5">Свойство</div>
				<div class="w7">Значение</div>
			</div>
			<div class="properties white-body">
				<div class="prop-item wblock white-block-row">
					<div class="w5">
						<select name="props[]" class="sel-prop">
							<option value="" selected>Выберите...</option>
							{if !empty($editable_properties)}
								{foreach from=$editable_properties item=prop name=propsList}
									{if $prop.unique != 1 && !($prop.fixed == '2' && $accountType == 'Admin') && !in_array($prop.key, array('price', 'old_price', 'prefix', 'available', 'available_variant', 'count', 'visible', 'variant_visible', 'price_variant', 'old_price_variant', 'variant_code', 'store_count'))}
										<option value="{$prop.id}">{$prop.title}</option>
									{/if}
								{/foreach}
							{/if}
						</select>
					</div>
					<div class="w6 block-value">
						<input type="text" name="values[]" class="new-val" data-disabled="1" disabled />
					</div>
					<div class="w1 action-button action-add" title="Добавить">
						<i class="icon-add"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
