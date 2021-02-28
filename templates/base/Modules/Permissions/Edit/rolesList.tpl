<div class="white-body">
{if !empty($roles)}
	{foreach from=$roles item=role}
		<div class="wblock white-block-row" data-id="{$role.id}" data-title="{$role.title}">
			<div class="w1">{$role.id}</div>
			<div class="w2">{$role.key}</div>
			<div class="w2">{$role.title}</div>
			<div class="w2">{$role.default_permission}</div>
			<div class="w3">{$role.after_login_redirect}</div>
			{*<a href="#" data-id="{$role.id}" data-key="{$role.key}" data-title="{$role.title}" data-perm="{$role.default_permission}" data-redir="{$role.after_login_redirect}" class="action-button action-edit w1" title="Редактировать"><i></i></a>
			<a href="#" data-id="{$role.id}" data-key="{$role.key}" data-title="{$role.title}" data-perm="{$role.default_permission}" data-redir="{$role.after_login_redirect}" class="action-button action-delete w1 m-border" title="Удалить"><i></i></a>*}
			<div class="w1 action-button action-edit" title="Редактировать"><i class="icon-edit"></i></div>
			<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
		</div>
	{/foreach}
{else}
	<div class="wblock white-block-row">
		<div class="w12">Роли еще не созданы</div>
	</div>	
{/if}
</div>
