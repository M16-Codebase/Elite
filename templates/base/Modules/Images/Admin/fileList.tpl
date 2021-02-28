{?$cant_upload = !$account->isPermission('images', 'upload')}
{if !isset($simple) && !empty($smarty.get.simple)}
	{?$simple = true}
{/if}
{if empty($simple)}
	
	{if !empty($images)}
		{foreach from=$images item=i name=gallery_images}
			{?$iCollection = $i->getCollection()}
			<li class="uploaded-image" id="img-{$i.id}" data-image-position="{$i.num}" data-image-id="{$i.id}">
				<div class="image-form a-hidden" data-action="/images/reload/">
					<input type="hidden" name="image_id" value="{$i.id}" />
					<input type="hidden" name="id" value="{$iCollection->getId()}" />
					<input type="file" name="image" id="reload-pic-{$i.id}" />
				</div>
				<div class="image-inner">
					<div class="gallery-top">
						{if empty($cant_upload)}
							<div class="drag-drop a-left"></div>
							<div class="uploaded-img-options a-right">
								<label for="reload-pic-{$i.id}" class="option reload a-left" title="Заменить изображение"><i></i></label>
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
						<a href="{$i->getUrl()}" class="fancybox" rel="gallery">
							<img src="{$i->getUrl(175, 100, true, true)}" alt="{$i.id}" />
						</a>
					</div>
					{if empty($cant_upload)}
						<div class="gallery-actions a-justify">
							<label class="option set-gallery" title="Показывать в галерее">
								<input type="checkbox" name="show[{$i.id}]" value="1" /><i></i>
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
							{?$cover = $iCollection->getCover()}
							<label class="set-cover" title="Сделать обложкой">
								<input type="radio" name="cover-{$iCollection->getId()}" value="{$i.id}"{if !empty($cover) && $cover.id == $i.id} checked{/if} /><i></i>
							</label>
						{/if}
					{/if}
				</div>
				<div class="image-hidden">
					<div class="hidden-row">
						<div data-action="/images/saveDescription/" class="image-form img-descr-form">
							<div class="hidden-title">Редактирование подписи</div>
							<input type="hidden" name="image_id" value="{$i.id}" />
							<input type="hidden" name="id" value="{$iCollection->getId()}" />
							<textarea name="image_text" rows="3">{if !empty($i.text)}{$i.text}{/if}</textarea>
							<div class="clear-descr a-right" title="Удалить подпись"><i></i></div>
							<div class="save-descr btn">Сохранить</div>
							<span class="close-descr">Отмена</span>
						</div>
					</div>	
				</div>
		{/foreach}
	{/if}
	{if empty($gallery) && !empty($iCollection)}{?$gallery = $iCollection}{/if}
	{if empty($cant_upload) && !empty($gallery)}
		<li class="add-new-image add-to-gallery">
			<div class="image-form a-hidden" data-action="/images/upload/">
				<input type="hidden" name="id" value="{$gallery->getId()}" />
				<input type="file" name="image" multiple id="new-pic-{$gallery->getId()}" />
			</div>
			<label for="new-pic-{$gallery->getId()}" class="image-inner action-button action-add">
				<i class="action-icon icon-add"></i>
				<div class="action-text">Добавить изображение</div>
			</label>
	{/if}
	
{else}
	
	{if !empty($images)}
		{foreach from=$images item=i name=gallery_images}
			{if empty($gallery)}
				{?$gallery = $i->getCollection()}
			{/if}
			<a href="{$i->getUrl()}" class="fancybox row-image" rel="img-{$gallery->getId()}">
				<img src="{$i->getUrl(70, 70, true, true)}" alt="{$i.id}" />
			</a>
		{/foreach}
	{/if}
	
{/if}