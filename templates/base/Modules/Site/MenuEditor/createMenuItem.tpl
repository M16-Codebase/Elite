<form action="/menu-editor/editMenuItem/" class="create-menu-item-form">
	<input type="hidden" name="menu_id" value='{$menu.id}'>
	<input type="hidden" name="parent_id">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Создание раздела меню</h1>
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
			<div class="wblock">
				<div class="white-block-row">
					<div class="w4">Заголовок</div>
					<div class="w8">
						<input type="text" name="name">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Подсказка</div>
					<div class="w8">
						<input type="text" name="title">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Ссылка</div>
					<div class="w8">
						 <input type="text" name="url">
					</div>
				</div>
				<div class="white-block-row">
					<div class="w4">Изображение</div>
					<div class="w8">
						<input type="file" name="image">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
