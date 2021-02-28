<div class="aside-panel">
	{include file="Admin/components/actions_panel.tpl"
		buttons = array(
			'save' => array(
				'text' => 'Сохранить текст'
			)
		)}
</div>
<div class="viewport">
	<input type="hidden" name="id" />
	<input type="hidden" name="post_id" />
	<input type="hidden" name="property_id" />
	<input type="hidden" name="status" class="status-input" />
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w11">
				<strong>Видимость</strong>
			</div>
			<div class="w1 action-button action-visibility action-{if !empty($post) && $post.status == 'close'}show{else}hide{/if}">
				<i class="icon-{if !empty($post) && $post.status == 'close'}show{else}hide{/if}"></i>
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">
				<strong>Аннотация</strong>
			</div>
			<div class="w9">
				<textarea name="annotation" rows="5"></textarea>
			</div>
		</div>
		<div class="wblock post-block">
			<textarea name="text" class="redactor"></textarea>
		</div>
	</div>
	{if !empty($post)}
		{include file="Modules/Posts/Admin/post_uploader.tpl" post=$post}
	{/if}
</div>
