{?$title = $theme_data.title}
{?$title = is_array($title) ? (isset($title[$request_segment['id']]) ? $title[$request_segment['id']] : 'unknown title') : $title}

<form id="edit-theme-form">
	<div class="content-top">
		<h1>Редактирование темы «{$title}»</h1>
		<div class="content-options">
			{?$menu_list = array(array('url'=>'/$moduleUrl/', 'title' => 'Темы'))}
			{include file='Admin/components/actions_panel.tpl'
				buttons = array(
				'back' => '/$moduleUrl/',
				'save' => array(
					'class' => 'submit'
				)
			)}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			{if $constants.segment_mode == 'none'}
				<div class="wblock white-block-row">
					<div class="w3">Название</div>
					<div class="w9"><input type="text" name="title" /></div>
				</div>
			{else}
				{foreach from=$segments item=seg}
					<div class="wblock white-block-row">
						<div class="w3">Название ({$seg.title})</div>
						<div class="w9"><input type="text" name="title[{$seg.id}]" /></div>
					</div>
				{/foreach}
			{/if}
			<div class="wblock white-block-row">
				<div class="w3">Ключевое слово</div>
				<div class="w9"><input type="text" name="keyword" disabled /></div>
			</div>
			{if !empty($themes_level) && !empty($parent_themes)}
				<div class="wblock white-block-row">
					<label class="w3" for="parent_id">
						<strong>Родительская тема</strong>
					</label>
					<div class="w9">
						<select name="parent_id">
							<option value="">Верхний уровень</option>
							{foreach from=$parent_themes item=p_theme}
								<option value="{$p_theme.id}">{$p_theme.title}</option>
							{/foreach}
						</select>
					</div>
				</div>
			{/if}
		</div>
	</div>
</form>
