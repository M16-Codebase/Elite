{?$allow_file_types = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'djvu', 'txt', 'rar', 'zip', 'rtf', 'ppt', 'pps', 'png', 'jpg', 'jpeg', 'gif', 'tiff', 'tif', 'xml', 'csv', 'swf')}

<div class="content-top">
	<h1>{if !empty($object)}Редактирование{else}Создание{/if} файла</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
				'back' => '#',
				'save' => array(
					'class' => 'submit'
				)
			)}
	</div>
</div>

<div class="content-scroll file-upload-block">
	<div class="white-blocks viewport">
		<input type="hidden" name="object_id" />
		<input type="hidden" name="entity_id" />
		<input type="hidden" name="segment_id" />
		<input type="hidden" name="property_id" />
		{if $constants.segment_mode == 'lang'}
			{foreach from=$segments item=seg}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>Заголовок ({$seg.title})</strong>
					</div>
					<div class="w9">
						<input type="text" name="title[{$seg.id}]" />
					</div>
				</div>
			{/foreach}
		{else}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Заголовок</strong>
				</div>
				<div class="w9">
					<input type="text" name="title" />
				</div>
			</div>
		{/if}
		<div class="wblock white-block-row">
			<div class="w3">
				<strong>Файл</strong>
			</div>
			<div class="w9">
				<input type="file" name="file" class="input-file"
					data-hash="{md5($hash_salt_string . time())}"
					{if !empty($property.values.max)} data-maxsize="{$property.values.max}"{/if} 
					data-format="{if !empty($property.values.format)}{$property.values.format}{else}{implode(',', $allow_file_types)}{/if}" />
				<input type="hidden" name="file_name" class="input-filename" />
				<input type="hidden" name="file_path" class="input-filepath" />
			</div>
			<div class="row-progress-bar"><div></div></div>
		</div>
	</div>
</div>