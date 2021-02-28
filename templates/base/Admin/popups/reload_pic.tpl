<div class="popup-window popup-reload-image uploader-popup">
	<form method="POST" action="/images/reload/" enctype="multipart/form-data">
		<input type="hidden" name="image_id" value="" />
		<input type="hidden" name="id" value="">
		<table class="ribbed">
			<tr>
				<td class="td-title">
					Изображение
				</td>
				<td>
					<input type="file" name="image" />
				</td>
			</tr>
		</table>
		<div class="buttons">
			<div class="submit a-button-green">Сохранить</div>
		</div>
                {if !empty($ext_type_field)}
                    <input type="hidden" name="custom_template" value="{$ext_type_field}">
                {/if}
	</form>
</div>