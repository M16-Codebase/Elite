<form action="/seo-sitemap/editRule/" method="POST" class="add-form">
	<div class="content-top">
		<h1>Редактирование правила{if !empty($rule.url)} для ссылки «{$rule.url}»{/if}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
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
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Ссылка</strong>
				</div>
				<div class="w9">
					<input type="hidden" name="id" />
					<input class="link-field" type="text" name="url" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Правило</strong>
				</div>
				<div class="w9">
					<select name="type">
						<option value="allow">Allow</option>
						<option value="disallow">Disallow</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>