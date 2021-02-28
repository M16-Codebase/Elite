{?$allow_edit = $accountType == 'SuperAdmin' || (!empty($user) && (empty($user['person_type']) || $user['person_type'] == 'man'))}
<form action="/users-edit/editUser/" class="edit-user-form user-fields">
	<input type="hidden" name="id" />
	<input type="hidden" name="person_type" />
	<div class="content-top">
		<h1>Редактирование пользователя id {$user.id}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'delete' => '#',
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
		</div>
	</div>
</form>