{?$cant_upload = !$account->isPermission('images', 'upload')}
{?$gallery_data = ''}
{if !empty($galleryImages)}
	{foreach from=$galleryImages item=i name=gallery_images}
		{?$gallery_data = $i.gallery_data}
		<li class="uploaded-image" id="img-{$i.filename}" data-image-position="{$i.num}" data-image-id="{$i.filename}">
			<div class="image-form a-hidden" data-action="/tmp-images/reload/">
				<input type="hidden" name="gallery_data" value="{$i.gallery_data}" />
				<input type="hidden" name="filename" value="{$i.filename}" />
				<input type="hidden" name="gallery_dir" value="{$gallery_dir}" />
				<input type="file" name="image" id="reload-pic-{$i.filename}" />
			</div>
			<div class="image-inner">
				<div class="gallery-top">
					{if empty($cant_upload)}
						<div class="drag-drop a-left"></div>
						<div class="uploaded-img-options a-right">
							<label for="reload-pic-{$i.filename}" class="option reload a-left" title="Заменить изображение"><i></i></label>
							<div class="option delete a-left" title="Удалить изображение"><i></i></div>
						</div>
					{/if}
				</div>
				<div class="gallery-image">
					{if empty($cant_upload)}
						<table class="gravity-table">
							<tr>
								<td data-gravity="TL"></td>
								<td data-gravity="T"></td>
								<td data-gravity="TR"></td>
							</tr>
							<tr>
								<td data-gravity="L"></td>
								<td data-gravity="C"></td>
								<td data-gravity="R"></td>
							</tr>
							<tr>
								<td data-gravity="BL"></td>
								<td data-gravity="B"></td>
								<td data-gravity="BR"></td>
							</tr>
						</table>
					{/if}
					<a href="{$i.url}" class="fancybox" rel="gallery">
						<img src="{$i.url}" alt="{$i.filename}" />
					</a>
				</div>
				{if empty($cant_upload)}
					<div class="gallery-actions a-justify">
						<label class="option set-gallery" title="Показывать в галерее">
							<input type="hidden" name="gallery_data" value="{$i.gallery_data}" />
							<input type="checkbox" name="show[{$i.filename}]" value="1" {if empty($i.hidden)}checked="checked" {/if}/><i></i>
						</label>
						<div class="optborder"></div>
						<div class="option img-descr{if !empty($i.text)} m-active{/if}" data-descr="{if !empty($i.text)}{$i.text}{/if}" title="{if !empty($i.text)}{$i.text}{else}Добавить подпись{/if}"><i></i></div>
						<div class="optborder"></div>
						<div class="option gravity m-{$i.gravity}" title="Выравнивание картинки"><i></i></div>
						<div class="optborder"></div>
						<div class="option crop" title="Обрезка картинки"><i></i></div>
					</div>
					{if empty($no_paste)}
						<ul class="gallery-actions paste-actions a-justify">
							<li class="option paste-button paste-left" title="Вставить слева"><i></i>
							<li class="optborder"></li>
							<li class="option paste-button paste-right" title="Вставить справа"><i></i>
							<li class="optborder"></li>
							<li class="option paste-button paste-center" title="Вставить по центру"><i></i>
						</ul>
					{/if}
					{if empty($no_cover)}
						<label class="set-cover" title="Сделать обложкой">
							<input type="radio" name="cover-{$i.filename}" value="{$i.filename}"{if !empty($i.cover)} checked{/if} /><i></i>
						</label>
					{/if}
				{/if}
			</div>
			<div class="image-hidden">
				<div class="hidden-row">
					<div data-action="/tmp-images/saveDescription/" class="image-form img-descr-form">
						<div class="hidden-title">Редактирование подписи</div>
						<input type="hidden" name="gallery_data" value="{$i.gallery_data}" />
						<input type="hidden" name="filename" value="{$i.filename}" />
						<input type="hidden" name="gallery_dir" value="{$gallery_dir}" />
						<textarea name="image_text" rows="3">{if !empty($i.text)}{$i.text}{/if}</textarea>
						<div class="clear-descr a-right" title="Удалить подпись"><i></i></div>
						<div class="save-descr btn">Сохранить</div>
						<span class="close-descr">Отмена</span>
					</div>
				</div>
			</div>
	{/foreach}
{/if}
{if empty($cant_upload) && !empty($gallery_dir)}
	<li class="add-new-image add-to-gallery">
		<div class="image-form a-hidden" data-action="/tmp-images/upload/">
			<input type="hidden" name="gallery_data" value="{$gallery_data}" />
			<input type="hidden" name="gallery_dir" value="{$gallery_dir}" />
			<input type="file" name="image" id="new-pic-{$gallery_dir}" />
		</div>
		<label for="new-pic-{$gallery_dir}" class="image-inner action-button action-add">
			<i class="action-icon icon-add"></i>
			<div class="action-text">Добавить изображение</div>
		</label>
{/if}