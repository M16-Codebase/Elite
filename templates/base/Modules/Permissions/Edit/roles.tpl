{?$pageTitle = "Роли — " . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Роли</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			buttons = array(
				'add' => array(
					'text' => 'Создать'
				)
			)}
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks page-content viewport">
		<div class="wblock white-header white-block-row">
			<div class="w1">ID</div>
			<div class="w2">Ключ</div>
			<div class="w2">Заголовок</div>
			<div class="w2">Доступ по умолчанию</div>
			<div class="w3">Редирект после авторизации</div>
			<div class="w1"></div>
			<div class="w1"></div>
		</div>
		{include file="Modules/Permissions/Edit/rolesList.tpl"}
	</div>
</div>
	
{*{capture assign=editBlock name=editBlock}*}
	{*<form class="add-role-form" action="/permissions/addRole/" method="POST">
		<div class="content-top">
			<h1>Создание роли</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Добавить',
						'url' => '#'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = handlers
					buttons = $buttons}
				{$handlers|html}
			</div>
		</div>
		<div class="content-scroll">
			<div class="white-blocks viewport">
				<div class="wblock white-block-row">
					<div class="w3">
						Ключ	
					</div>
					<div class="w9">
						<input type="text" name="key" />	
					</div>	
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						Название	
					</div>
					<div class="w9">
						<input type="text" name="title" />
					</div>	
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						Доступ по умолчанию	
					</div>
					<div class="w9">
						<input type="hidden" name="default_permission" value="disable" />
						<input type="checkbox" name="default_permission" value="able" />
					</div>						
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						Редирект после авторизации	
					</div>
					<div class="w9">
						<input type="text" name="after_login_redirect" class="title_input input" />
					</div>						
				</div>						
			</div>
		</div>
	</form> *}
	{*<form class="edit-role-form" action="/permissions/editRole/" method="POST">
		<div class="content-top">
			<h1>Редактирование</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Сохранить',
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
				<div class="wblock white-block-row">
					<div class="w3">
						Название	
					</div>
					<div class="w9">
						<input type="hidden" name="id" />
						<input type="text" name="title" />
					</div>	
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						Редирект после авторизации	
					</div>
					<div class="w9">
						<input type="text" name="after_login_redirect" />
					</div>	
				</div>	
			</div>
		</div>	
	</form>		*}
{*{/capture}*}