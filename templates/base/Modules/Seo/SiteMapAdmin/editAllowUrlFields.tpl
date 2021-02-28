<form class="add-allow-url" action="/seo-sitemap/editAllowUrl/" method="POST">
	<div class="content-top">
		<h1>Редактирование URL{if !empty($url_data.url)} «{$url_data.url}»{/if}</h1>
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
					URL
				</div>
				<div class="w9">
					<input type="hidden" name="id" />
					<input type="text" name="url" />
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
					<input type="text" name="date" class='datepicker short'/>
					<input type="text" name="time" class='short mask' data-mask='99:99:99'/>
				</div>
			</div>
		</div>
	</div>
</form>