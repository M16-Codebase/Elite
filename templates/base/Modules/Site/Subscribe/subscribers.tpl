{?$pageTitle = $group.name . ' — Списки рассылки — ' . (!empty($confTitle) ? $confTitle : '')}
{?$admin_page = 1}
<div class="content-top">
	<h1>{$group.name}</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = (($group.main_list == 1) ? 
				array(
					'back' => '/subscribe/subscribersLists/',
					'add' => array(
						'class' => 'show-create',
						'data' => array(
							'id' => $group.id
						)
					),
					'import' => array(
						'text' => 'Импорт CSV',
						'data' => array(
							'id' => $group.id
						)
					),
					'export' => array(
						'text' => 'Экспорт CSV',
						'url' => $smarty.server.REQUEST_URI . '&export',
						'data' => array(
							'id' => $group.id
						)
					),
					'another' => array(
						'text' => 'В другой список',
						'data' => array(
							'id' => $group.id,
						)
					)
				)
				:
				array(
					'back' => '/subscribe/subscribersLists/',
					'add' => array(
						'class' => 'show-create',
						'data' => array(
							'id' => $group.id
						)
					),
					'import' => array(
						'text' => 'Импорт CSV',
						'data' => array(
							'id' => $group.id
						)
					),
					'export' => array(
						'text' => 'Экспорт CSV',
						'url' => $smarty.server.REQUEST_URI . '&export',
						'data' => array(
							'id' => $group.id
						)
					),
					'another' => array(
						'text' => 'В другой список',
						'data' => array(
							'id' => $group.id,
						)
					),
					'delete' => array(
						'text' => 'Очистить список',
						'data' => array(
							'id' => $group.id
						)
					)
				)
			)
		)}	
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport" data-group_id="{$group.id}">
		<div class="white-blocks list">
		{if !empty($members)}
			{include file="Modules/Site/Subscribe/subscribersList.tpl"}
		{/if}
		</div>
		{if !empty($members)}
			{include file="Admin/components/paging.tpl"}
		{/if}
	</div>
</div>
{capture assign=aside_filter name=aside_filter}
	<section class="aside-filter">
		<form class="user-form items-filter" method="GET" action="/subscribe/subscribersList/">
			<input type="hidden" name="sort" class="input-sort" />
			<input type="hidden" name="group_id" value="{$group.id}" />
			<div class="field">
				<div class="f-title">Фамилия</div>
				<div class="f-input">
					<input type="text" name="surname" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Наименование организации</div>
				<div class="f-input">
					<input type="text" name="company_name" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">E-mail</div>
				<div class="f-input">
					<input type="text" name="email" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Статус</div>
				<div class="f-input cbx">
					<label><input type="checkbox" name="status[new]" value="new">&nbsp;Не подтвержден</label>
					<label><input type="checkbox" name="status[active]" value="active">&nbsp;Активен</label>
					<label><input type="checkbox" name="status[lock]" value="lock">&nbsp;Заблокирован</label>
				</div>
			</div>
			<div class="field">
				<div class="f-title">Дата добавления</div>
				<div class="f-input between">
					<input type="text" name="date_min" mask="99.99.9999" class="datepicker a-left">
					<input type="text" name="date_max" mask="99.99.9999" class="datepicker a-right">
				—
				</div>
			</div>
			<div class="buttons">
				<button class="submit btn btn-main a-block">Показать</button>
				<div class="link-cont">
					<span class="clear-form a-link small-descr">Сбросить фильтр</span>
				</div>
			</div>
		</form>
	</section>
{/capture}
{include file="/Modules/Site/Subscribe/subscriberFields.tpl" assign=add_url}
{include file="/Modules/Site/Subscribe/subscribersImport.tpl" assign=import_csv}
{capture assign=editBlock name=editBlock}
	{$add_url|html}
	{$import_csv|html}
{/capture}
