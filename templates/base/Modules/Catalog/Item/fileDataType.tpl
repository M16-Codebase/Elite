<form action="/catalog-item/editFileDataType/"{if !empty($smarty.get.segment_title)} data-segment="{$smarty.get.segment_title}"{/if}>
	<input type="hidden" name="id" class="id-input" />
	<input type="hidden" name="segment_id" class="segment-id-input" />
	<input type="hidden" name="property_id" class="prop-id-input" />
	<input type="hidden" name="file_id" class="file-id-input" />
	<div class="sended success">
		Файл загружен <span class="small-descr close a-link">(Изменить)</span>
	</div>
	<div class="deleted success">
		Файл удален <span class="small-descr close a-link">(Загрузить новый)</span>
	</div>
	<table class="ribbed">
		<tr>
			<td class="td-title">
				{if !empty($file)}
					<a href="{$file->getUrl()}" target="_blank" title="{$file.name}.{$file.ext}">Флаер</a>
				{else}
					Флаер
				{/if}
				{if !empty($smarty.get.segment_title)} ({$smarty.get.segment_title}){/if}
			</td>
			<td>
				<input type="file" name="file" />
			</td>
			<td class="td-submit">
				<button class="a-button-blue submit">Загрузить</button>
			</td>
			<td class="td-small">				
				<div class="table-btn delete{if empty($file)} a-hidden{/if}" title="Удалить"></div>				
			</td>
		</tr>
	</table>
</form>