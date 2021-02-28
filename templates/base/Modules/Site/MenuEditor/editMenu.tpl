<form action="/menu-editor/editMenu/" class="edit-menu-form">
	<input type="hidden" name="id" class="edit-menu-id">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Переименовать меню:</h1>
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
	<div class="selected-items a-hidden"></div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="white-header wblock white-block-row m-compact">
				<div class="w5">
					Ключ
				</div>
				<div class="w7"></div>
			</div>
			<div class="wblock white-block-row">
				<div class="w5">
					<input type="text" name='key' class='edit-key'>
				</div>
				<div class="w7"></div>
			</div>
		</div>
	</div>
</form>
