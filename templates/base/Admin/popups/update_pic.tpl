<div class="popup-window popup-reload-image uploader-popup">
	<form method="POST" action="/images/update/" enctype="multipart/form-data">
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
		</table>
		<div class="buttons">
			<div class="submit a-button-green">Сохранить</div>
		</div>
                {if !empty($ext_type_field)}
                    <input type="hidden" name="custom_template" value="{$ext_type_field}">
                {/if}
	</form>
</div>