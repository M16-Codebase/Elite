<ul class="img-uploader-gallery{if !empty($no_paste)} m-no-paste{/if}{if !empty($no_bottom)} m-no-bottom{/if}"	{if !empty($gallery_dir)} id="collection-{$gallery_dir}" data-temp_dir="{$gallery_dir}"{/if}>
	{include file="Modules/Images/TmpImages/fileList.tpl"
		images = (!empty($gallery)? $gallery->getImages() : '')}
</ul>