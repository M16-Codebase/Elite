<div class="post-images-uploader tmp-gallery">
	<div class="img-uploader-header">
		<h3 class="upload-title">Изображения для вставки</h3>
		<p>Поставьте курсор в нужное место в верхнем поле, наведите указатель мыши на изображение и нажмите кнопку «Вставить слева» или «Вставить справа».</p>
	</div>
	{include file="Modules/Images/TmpImages/files_uploader.tpl"
		gallery_dir = (!empty($create_post_hash)? $create_post_hash : '')
	}	
</div>