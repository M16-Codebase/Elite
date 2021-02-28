<form action="/seo/" class="add-meta-form">
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Создание META-тегов</h1>
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
					<strong>UID</strong>
				</div>
				<div class="w9">
					<input type="text" name='page_uid'>
				</div>
			</div>
		</div>
	</div>
</form>
