<form action="{if !empty($actionedit)}{$actionedit}{else}/seo/addLink/{/if}" class="add-links-form">
	<input type="hidden" name="id">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>{if !empty($actionedit)}Редактирование{else}Создание{/if} ссылки для перелинковки</h1>
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
					Ключевая фраза
				</div>
				<div class="w9">
					<input type="text" name="phrase">
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					url
				</div>
				<div class="w9">
					<input type="text" name="url">
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Ссылок на страницу
				</div>
				<div class="w9">
					<input type="text" name="page_limit">
				</div>
			</div>
		</div>
	</div>
</form>
