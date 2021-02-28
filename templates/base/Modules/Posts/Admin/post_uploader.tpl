<div class="post-images-uploader">
	<div class="img-uploader-header">
		<h3 class="upload-title">Изображения для вставки</h3>
		<p>Поставьте курсор в нужное место в верхнем поле, наведите указатель мыши на изображение и нажмите кнопку «Вставить слева» или «Вставить справа».</p>
	</div>
	{include file="Modules/Images/Admin/files_uploader.tpl" 
		gallery = (!empty($post.gallery)? $post.gallery : '')
	}	
</div>