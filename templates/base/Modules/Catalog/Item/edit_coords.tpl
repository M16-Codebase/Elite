<form class="set-coords-form">
	<div class="content-top">
		<h1>Указатель на карте</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить'
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
				<div class="w12">
					<input type="text" class="coords-input" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w12">
					<div class="map" style="height: 400px;"></div>
				</div>
			</div>
		</div>
	</div>
</form>