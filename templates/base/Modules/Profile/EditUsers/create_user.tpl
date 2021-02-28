<form action="/users-edit/createUser/" class="creat-user-form user-fields">
	<div class="content-top">
		<h1>Создание {if $current_person_type == "man"}администратора{else}пользователя{/if}</h1>
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
			{include file="Modules/Profile/EditUsers/userFields.tpl" create_user=true allow_edit=$account->isPermission('users-edit', 'createUser')}
		</div>
	</div>
</form>
