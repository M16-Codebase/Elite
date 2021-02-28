{?$create_post_hash=md5(time())}
{?$post = NULL} {* Костыль для избавления от попадания в форму выведенных ранее постов *}
<form action="/{$moduleUrl}/editPost/" class="add-form">
	{if (!empty($current_theme_id))}
		<input type="hidden" name="theme_id" value="{$current_theme_id}">
	{/if}
	{if !empty($smarty.get.s)}
		<input type="hidden" name="segment_id" value="{$smarty.get.s}">
	{/if}
	<div class="content-top">
		{if !empty($site_link)}<h1>{if $site_link == '/blog/'}Новая запись в блоге{elseif $site_link == '/pages/'}Новая статья{elseif $site_link == '/news/'}Новая новость{/if}</h1>{/if}
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Создать',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
			assign = addFormButtons
			buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="viewport">
			{include file="Modules/Posts/Pages/innerEditForm.tpl"}
			{include file="Modules/Posts/Admin/new_post_images.tpl" is_gallery=0 select_preview=1}
		</div>
	</div>
</form>