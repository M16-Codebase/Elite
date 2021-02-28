<form class="add-allow-url" action="/seo-sitemap/addAllowUrls/" method="POST">
	<div class="content-top">
		<h1>Создание URL</h1>
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
					URLs
				</div>
				<div class="w9">
					<textarea name="urls" rows="5"></textarea>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Приоритет
				</div>
				<div class="w9">
					<select name="priority">
						{foreach from=$priority_list item=priority_value}
							<option value="{$priority_value}">{$priority_value}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					Последнее изменение
				</div>
				<div class="w9">
					{*<input type="text" name="last_modification">*}
					<input type="text" name="date" class='datepicker-init short'/>
					<input type="text" name="time" class='short mask' data-mask='99:99:99'/>
				</div>
			</div>
		</div>
	</div>
</form>