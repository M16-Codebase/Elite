<form action="/segment-text/create/" class="add-url-section-form">
	<input type="hidden" name="page_url_id" value="{$url_data.id}" />
	<div class="popup-preloader"></div>
	<div class="content-top">
		<h1>Новый текст</h1>
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
					<strong>Заголовок</strong>
				</div>
				<div class="w9">
					<input type="text" name="title" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Ключ</strong>
				</div>
				<div class="w9">
					<input type="text" name="key" />
				{*{if !empty($std_posts_keys)}*}
					{*<select name="key">*}
						{*{foreach from=$std_posts_keys key=post_key item=position_title}*}
							{*<option value="{$post_key}">{$position_title}</option>*}
						{*{/foreach}*}
					{*</select>*}
				{*{/if}*}
				</div>
			</div>
			{if $constants.segment_mode != 'none'}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Сегмент</strong>
				</div>
				<div class="w9">
					<select name="segment_id">
						{if $constants.segment_mode != 'lang'}<option value="">Для всех</option>{/if}
						{foreach from=$segments item=s}
							<option value="{$s.id}">Только {$s.title}</option>
						{/foreach}
					</select>
				</div>
			</div>
			{/if}

			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Тип текста</strong>
				</div>
				<div class="w9">
					<select name="full_version">
						<option value="1" selected="selected">С форматированием</option>
						<option value="0">Без форматирования</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>