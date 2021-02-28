<form action="/segment-text/editPageUrl/" class="{if !empty($form_class)}{$form_class}{else}add-url-form{/if}">
	<input type="hidden" name="id" class="edit-menu-id">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>{if !empty($form_class)}Редактирование группы текстов{else}Новая группа текстов{/if}</h1>
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
					<strong>Название</strong>
				</div>
				<div class="w9">
					<input type="text" name='title'>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>URL</strong>
				</div>
				<div class="w9">
					<input type="text" name='url'>
				</div>
			</div>
		</div>
	</div>
</form>
