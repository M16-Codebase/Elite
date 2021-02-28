{?$includeJS.editor = "Modules/Posts/Pages/edit.js"}
<form id="edit_post_form" action="/{$moduleUrl}/editPost/">
	{include file="Modules/Posts/Pages/innerEditForm.tpl"}
</form>
{include file="Modules/Posts/Admin/post_uploader.tpl" is_gallery=0 select_preview=1}