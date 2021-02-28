<ul class="img-uploader-gallery{if !empty($no_paste)} m-no-paste{/if}{if !empty($no_bottom)} m-no-bottom{/if}"	{if !empty($gallery)} id="collection-{$gallery->getId()}" data-collection-id="{$gallery->getId()}"{/if}>
	{include file="Modules/Images/Admin/fileList.tpl"
		images = (!empty($gallery)? $gallery->getImages() : '')}
</ul>