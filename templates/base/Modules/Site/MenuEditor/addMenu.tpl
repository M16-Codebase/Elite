<form action="/menu-editor/addMenu/" class="add-menu-form">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Добавление меню</h1>
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
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Ключ</strong>
				</div>
				<div class="w9">
					<input type="text" name='key'>
				</div>
			</div>
		</div>
	</div>
</form>
