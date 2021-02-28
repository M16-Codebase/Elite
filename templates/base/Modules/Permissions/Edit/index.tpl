{?$pageTitle = 'Допуски — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Допуски</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
		multiple = true
		buttons =array(
			'back' => '/site/',
			'import' => array(
				'text' => 'Импорт CSV',
				'class' => 'to-import',
				'url' => '#'
			),
			'export' => array(
				'text' => 'Экспорт CSV',
				'url' => '/permissions/exportPermissions/'
			)
		)}
	</div>
</div>

<div class="content-scroll">
	<div class="permissions-container white-blocks viewport">
		{if !empty($roles)}
			<div class="wblock white-block-row white-header">
				<div class="w3">Методы</div>
				<div class="w9 roles-scroll-cont">
					<div class="roles-scroll a-inline-cont">
						{foreach from=$roles item=$role}
							<div>{$role.key}</div>
						{/foreach}
					</div>
				</div>
			</div>
			<div class="white-body">
				{include file="Modules/Permissions/Edit/actionsTabContent.tpl" actions=$actions_public}
				{include file="Modules/Permissions/Edit/actionsTabContent.tpl" actions=$actions_admin}
			</div>
		{/if}
	</div>
</div>


			
{capture assign=editBlock name=editBlock}
    <form class="import-csv-form" action="/permissions/importPermissions/" enctype="multipart/form-data">
		<div class="content-top">
			<h1>Импорт прав доступа из CSV-файла</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Сохранить',
						'class' => 'save-import',
						'url' => '#'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = importHandlers
					buttons = $buttons}	
				{$importHandlers|html}
			</div>
		</div>
		<div class="content-scroll">
			<div class="white-blocks viewport">
				<div class="wblock white-block-row">
					<div class="w3 inline td-title">Файл csv</div>
					<div class="w9">
						<input type="file" name="file" />
						{*<div style="margin-top: 6px;"><a href="#" class="small-descr" target="_blank">Скачать образец csv-файла</a></div>*}
					</div>
				</div>
			</div>	
		</div>			
    </form>
    <form class="rename-form" action="/permissions/renameAction/">
		<div class="content-top">
			<h1>Переименовать Аction</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array(
						'text' => 'Отмена',
						'class'=> 'rename-close'
					),
					'save' => array(
						'text' => 'Сохранить',
						'class'=> 'save-rename',
						'url' => '#'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = renameHandlers
					buttons = $buttons}	
				{$renameHandlers|html}
			</div>
		</div>	
		<div class="white-blocks">
			<div class="wblock white-block-row">
				<div class="w3 td-title">Заголовок</div>
				<div class="w9">
					<input type="hidden" name="id" />
					<input type="text" name="title" />
				</div>
			</div>
		</div>
    </form>
{/capture}