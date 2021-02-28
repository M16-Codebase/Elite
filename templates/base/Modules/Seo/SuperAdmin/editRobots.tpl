{?$pageTitle = 'Редактирование robots.txt — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Редактирование robots.txt</h1>
	<div class="content-options">
		{?$buttons = array(
			'save' => array(
				'text' => 'Сохранить',
				'class' => 'submit'
			)
		)}
		{include file="Admin/components/actions_panel.tpl"
			assign = editRobotsButtons
			buttons = $buttons}
		{$editRobotsButtons|html}
	</div>
</div>
<div class="content-scroll">
	<div class="viewport">
		{if empty($error)}
			<form class="white-blocks robots-form">
				<textarea class="robots-area" name="text"></textarea>
			</form>
		{else}
			<div class="empty_content_message">Файл недоступен для редактирования.</div>
		{/if}
	</div>	
</div>	