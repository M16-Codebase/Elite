<form class="add-role-form" action="/permissions/editRole/" method="POST">
	<input type="hidden" name="id" />
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}роли{if !empty($role.title)} «{$role.title}»{/if}</h1>
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
				assign = handlers
				buttons = $buttons}
			{$handlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			{if empty($smarty.post.id)}
			<div class="wblock white-block-row">
				<div class="w3">
					Ключ	
				</div>
				<div class="w9">
					<select name="key">
						<option value="">Выберите...</option>
						{foreach from=$allow_roles item=role_key}
							<option value="{$role_key}"{if in_array($role_key, $used_roles)} disabled="disabled"{/if}>{$role_key}</option>
						{/foreach}
					</select>
				</div>
			</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w3">
					Название	
				</div>
				<div class="w9">
					<input type="text" name="title" />
				</div>	
			</div>
			{if empty($smarty.post.id)}
			<div class="wblock white-block-row">
				<div class="w3">
					Доступ по умолчанию	
				</div>
				<div class="w9">
					<input type="hidden" name="default_permission" value="disable" />
					<input type="checkbox" name="default_permission" value="enable" />
				</div>
			</div>
			{/if}
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
</form> 