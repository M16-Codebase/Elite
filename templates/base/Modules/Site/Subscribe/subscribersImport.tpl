<form class="subscribers-import-form" action="/subscribe/importSubscribers/" enctype="multipart/form-data">
	<input type="hidden" name="group_id" value="{$group.id}">
	<div class="content-top">
		<h1>Импорт подписчиков из CSV-файла</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'url' => '#',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = createSubscribersHandlers
				buttons = $buttons}	
			{$createSubscribersHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3"><strong>Файл CSV</strong></div>
				<div class="w9">
					<input type="file" name="file" />
					{?$sample_file = $site_config->get('sendsay_sample')}
					{if !empty($sample_file)}
						<a href="{$sample_file->getUrl()}" class="small-descr" target="_blank">Скачать образец csv-файла</a>
					{/if}
				</div>
			</div>
		</div>
	</div>
</form>