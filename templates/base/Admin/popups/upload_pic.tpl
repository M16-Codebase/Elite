<div class="popup-window popup-upload-image">
	<form method="POST" action="/images/upload/" enctype="multipart/form-data">
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
			{foreach from=$segments item=s}
				<tr>
					<td class="td-title">
						Описание ({$s.title})
					</td>
					<td>
						<textarea name="image_text" data-segment="{$s.id}" class="pic-descr" rows="3"></textarea>
					</td>
				</tr>
			{/foreach}	
                        {if !empty($pic_description_url) && $pic_description_url == TRUE}
                            <tr>
                                <td class="td-title">
                                    Описание (Ссылка в портфолио)
                                </td>
                                <td>
                                    <input type="text" name="url" class="pic-descr">
                                </td>
                            </tr>
                        {/if}
                        {if !empty($ext_type_field)}
                            <input type="hidden" name="custom_template" value="{$ext_type_field}">
                        {/if}
		</table>
		<div class="buttons">
			<button class="submit a-button-green">Сохранить</button>
		</div>
	</form>
</div>