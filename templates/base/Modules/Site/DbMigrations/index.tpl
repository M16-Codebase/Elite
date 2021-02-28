{?$pageTitle = 'Миграции базы данных — ' . (!empty($confTitle) ? $confTitle : '')}
{?$results = true}
{capture name=migrations_list assign=migrations_list}
	{if !empty($migrations_list)}
		<div class="white-header wblock white-block-row">
			<div class="w6">filename</div>
			<div class="w6">accepted</div>
		</div>
		<div class="white-body">
			{foreach from=$migrations_list item=m}
				<div class="wblock white-block-row">
					<div class="w6">{$m.name}</div>
					<div class="w6">{if $m.loaded}<i class="icon-check"></i>{/if}</div>
				</div>
			{/foreach}
		</div>
		{else}
		<div class="no-results">
			Миграций нет
			{?$results = false}
		</div> 
	{/if}
{/capture}

<div class="content-top">
	<h1>Миграции базы данных</h1>
	<div class="content-options">
		{if !empty($results)}
			{?$buttons = !empty($is_local)? array(
				'back' => array(
					'text' => 'Назад',
					'url' => !empty($smarty.server["HTTP_REFERER"])? $smarty.server["HTTP_REFERER"] : "/"
				),
				'save' => array(
					'text' => 'Применить миграции',
					'class' => 'apply-migrations',
					'url' => '/db-migrations/applyMigrations/'
				),
				'add' => array(
					'text' => 'Добавить миграции'
				)
			) : array(
				'back' => array(
					'text' => 'Назад',
					'url' => !empty($smarty.server["HTTP_REFERER"])? $smarty.server["HTTP_REFERER"] : "/"
				),
				'save' => array(
					'text' => 'Применить миграции',
					'class' => 'apply-migrations',
					'url' => '/db-migrations/applyMigrations/'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = handlers
				buttons = $buttons}	
			{$handlers|html}		
		{/if}
	</div>
</div>
<div class="content-scroll">
	<div class="migrations-list white-blocks viewport">
		{$migrations_list|html}
	</div>
</div>
		
{if !empty($is_local)}
	{capture assign=editBlock name=editBlock}
		{* Миграции создавать можно только на локалке *}
		<form class="add-migration-form" action="/db-migrations/addMigration/">
			<div class="content-top">
				<h1>Добавление миграции</h1>
				<div class="content-options">
					{?$buttons = array(
						'back' => array('text' => 'Отмена'),
						'save' => array(
							'text' => 'Сохранить',
							'class' => 'submit',
							'url' => '#'
						)
					)}
					{include file="Admin/components/actions_panel.tpl"
						assign = editHandlers
						buttons = $buttons}	
					{$editHandlers|html}
				</div>
			</div>
			<div class="content-scroll">
				<div class="white-blocks viewport">
					<div class="wblock">
						<div class="w12">
							<textarea class="migration-content" name="sql" rows="25"></textarea>
						</div>
					</div>
				</div>
			</div>	
		</form>
	{/capture}
{/if}