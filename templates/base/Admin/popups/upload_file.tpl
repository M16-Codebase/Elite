<div class="popup-window files-popup popup-upload-file">
	<form action="/files-edit/addFile/" enctype="multipart/form-data">
		<table class="ribbed">
			{if !empty($types)}
				<tr>
					<td class="td-title">Тип файла</td>
					<td>
						<select class="chosen fullwidth" name="type">
							{foreach from=$types key=id item=title}
								<option value="{$id}"{if $smarty.get.type == $id} class="default" selected{/if}>{$title}</option>
							{/foreach}
						</select>
					</td>
				</tr>
			{/if}
			<tr>
				<td class="td-title">Название</td>
				<td class="v-center">
					<input type='text' name='title' />
				</td>
			</tr>
			<tr>
				<td class="td-title">Файл</td>
				<td class="v-center"><input type="file" name="ufile" /></td>
			</tr>
			<tr>
				<td class="td-title">Сегмент</td>
				<td class="v-center">
					<select name="segment_id" class="chosen fullwidth">
						{foreach from=$segments item=s}
							<option value="{$s.id}"{if (empty($smarty.get.s) && $s.id == 1) || (!empty($smarty.get.s) && $s.id == $smarty.get.s)} class="default"{/if}>{$s.title}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td class="td-title">Нельзя скачать анонимно</td>
				<td class="v-center"><input type="checkbox" name="known_downloader" value="1" /></td>
			</tr>
		</table>
		<div class="buttons">
			<button class="a-button-green">Загрузить</button>
			<div class="close">или <span class="close-popup a-link">Отменить</span></div>
		</div>
	</form>
</div>