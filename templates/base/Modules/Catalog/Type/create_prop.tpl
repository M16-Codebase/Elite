<form action="/catalog-type/createProp/" class="create-prop-form">
	<input type="hidden" name="item_type_id" value="{$current_type.id}" />
	<div class="content-top">
		<h1>Создание нового свойства</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Создать',
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
					<input type="text" name="title" />
				</div>
			</div>
			{if $accountType == 'SuperAdmin'}
				<div class="wblock white-block-row">
					<label class="w12">
						<input type="checkbox" name="segment" />
						<span>Сегментированное</span>
					</label>
				</div>
			{/if}
		</div>
	</div>
</form>
