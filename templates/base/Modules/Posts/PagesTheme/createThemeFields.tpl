<form class="create-theme" action="/{$moduleUrl}/createTheme/" method="post">
	<div class="content-top">
		<h1>Новая тема</h1>
		<div class="content-options">
			{?$buttons = array(
			'back' => array('text' => 'Отмена'),
			'save' => array(
				'text' => 'Создать',
				'class' => 'submit',
				'url' => '#'
				)
			)}
			{include file="Admin/components/actions_panel.tpl" assign=editHandlers buttons=$buttons}
			{$editHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			{if $constants.segment_mode == 'none'}
				<div class="wblock white-block-row">
					<label class="w3" for="title">
						<strong>Заголовок</strong>
					</label>
					<div class="w9">
						<input type="text" name="title" />
					</div>
				</div>
			{else}
				{foreach from=$segments key=s_id item=seg}
					<div class="wblock white-block-row">
						<div class="w3">
							<strong>Заголовок</strong> ({$seg.title})
						</div>
						<div class="w9">
							<input type="text" name="title[{$s_id}]" />
						</div>
					</div>
				{/foreach}
			{/if}
			{if !empty($current_theme)}
				<input type="hidden" name="parent_id" value="{$current_theme.id}" />
			{/if}
		</div>
	</div>
</form>