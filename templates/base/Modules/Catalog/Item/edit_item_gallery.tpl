<div class="collection-block{if empty($gallery)} origin{/if}">
	<div class="post-images-uploader">
		{include file="Modules/Images/Admin/files_uploader.tpl"
			gallery = (!empty($gallery)? $gallery : '')
			no_paste = true}
	</div>
</div>