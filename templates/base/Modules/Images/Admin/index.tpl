{?$admin_page = 1}
{?$pageTitle = 'Картинки по-умолчанию — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Картинки по-умолчанию</h1>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		{if !empty($collection)}
			{?$images = $collection->getImages()}
			{if !empty($images)}
				<div class="wblock white-block-row post-images-uploader">
					{include file="Modules/Images/Admin/files_uploader.tpl"
						gallery = $collection
						no_bottom = true
						no_paste = true
					}
				</div>
			{else}
				Изображения не созданы.
			{/if}
		{else}
			Коллекция не создана
		{/if}
	</div>
</div>
