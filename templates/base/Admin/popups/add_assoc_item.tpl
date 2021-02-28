<div class="popup-window popup-add-assoc" data-title="Добавить сопутствующий товар">
	<form action="/catalog-item/itemAssoc/">
        <input type="hidden" name="id" value="{$catalog_item.id}" />
		<table class="ribbed">
			<tr>
				<td class="td-title">
					ID товара
				</td>
				<td>
					<input type="text" name="add_item" />
				</td>
			</tr>
		</table>
		<div class="buttons clearbox">
			<div class="submit a-button-green">Добавить</div>
		</div>
	</form>
</div>