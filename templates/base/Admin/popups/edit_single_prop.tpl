<div class="popup-window popup-single-prop" data-title="Изменение свойств">
	<div class="popup-preloader"></div>
	<div class="edit-title">
		Выбрано товаров: <sapn class="count"></sapn>
	</div>
	<form action="/catalog-item/changeFilteredItemsProp/">
		<input type="hidden" name="type_id" value="{$current_type.id}" />
		<div class="current-form a-hidden"></div>
		<div class="selected-items a-hidden"></div>
		<div class="single-prop-cont">
			<table class="single-prop-table ribbed">
				<thead>
					<tr>
						<th>Свойство</th>
						<th colspan="2">Значение</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<select name="props[]" class="sel-prop">
								<option value="">Выберите...</option>
								{foreach from=$editable_properties item=prop name=propsList}
                                    {if $prop.unique != 1 && !in_array($prop.key, array('price', 'old_price', 'prefix', 'available', 'available_variant', 'count', 'visible', 'variant_visible', 'price_variant', 'old_price_variant', 'variant_code', 'store_count'))}
                                        <option value="{$prop.id}">{$prop.title}</option>
                                    {/if}
								{/foreach}
							</select>
						</td>
						<td class="td-value">
							<input type="text" name="values[]" class="new-val" />
						</td>
						<td class="small">
							<div class="table-btn add"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>				
		<div class="buttons clearbox">
			<div class="submit a-button-green">Сохранить</div>
		</div>				
	</form>
</div>