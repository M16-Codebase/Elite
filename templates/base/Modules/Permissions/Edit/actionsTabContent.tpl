{if !empty($actions)}
	{?$module_url = NULL}
	{foreach from=$actions item=act}
		{if !empty($act.module_url)}
			{if empty($module_url) || $module_url != $act.module_url}
				{if !empty($module_url)}
					</div>
				{/if}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w3">{if !empty($module_titles[$act.module_url])}{$module_titles[$act.module_url]}{else}{$act.module_url}{/if}</div>
						<div class="w9 roles-scroll-cont">
							<div class="roles-scroll a-inline-cont">
								{foreach from=$roles item=role}
									<div>
										<input type="checkbox" />
									</div>
								{/foreach}
							</div>
						</div>
					</div>
					{?$module_url = $act.module_url}
				{/if}
				<div class="white-block-row action-row" data-id="action-{$act.id}">
					<div class="w3">/{$act.module_url}/{$act.action}/</div>
					<div class="w9 roles-scroll-cont">
						<div class="roles-scroll a-inline-cont">
							{foreach from=$roles item=role}
								<div>
									<input class="user_permission_checkbox" data-role_id="{$role.id}" data-action_id="{$act.id}" type="checkbox" name="permission[{$role.id}][{$act.id}]"
										{if (!empty($permissions[$role.key][$act.module_url][$act.action]) && $permissions[$role.key][$act.module_url][$act.action] == 'enable') || (empty($permissions[$role.key][$act.module_url][$act.action]) && ($role['default_permission'] == 'enable' || $act.admin == 0))} checked="checked"{/if}
									/>
								</div>
							{/foreach}
						</div>
					</div>
				</div>
			{/if}
		{/foreach}
	</div>
{/if}

{*
<table class="ribbed">
	{if !empty($roles)}
		<tr>
			<th></th>
			<th></th>
			<th></th>
			{foreach from=$roles item=$role}
				<th>{$role.key}</th>
			{/foreach}
		</tr>
		{if !empty($actions)}
			{?$module_url = NULL}
			{foreach from=$actions item=act}
				{if !empty($act.module_url)}
					{if empty($module_url) || $module_url != $act.module_url}
						{?$module_url = $act.module_url}
						{* здесь галки для групповой настройки прав доступа *}{*
						<tr>
							<td>{if !empty($module_titles[$act.module_url])}{$module_titles[$act.module_url]}{else}{$act.module_url}{/if}</td>
							<td></td>
							<td></td>
							{foreach from=$roles item=role}
								<td>
									<input type="checkbox"/>
								</td>
							{/foreach}
						</tr>
					{/if}
					<tr id="action-{$act.id}">
						<td>/{$act.module_url}/{$act.action}/</td>
						<td>
							<span class="action-title">{$act.title}</span>
						</td>
						<td>
							<a href="#" class="rename-actions-btn i-edit" title="Переименовать" data-id="{$act.id}" data-title="{$act.title}">rename</a>
						</td>
						{foreach from=$roles item=role}
							<td>
								<input class="user_permission_checkbox" data-role_id="{$role.id}" data-action_id="{$act.id}" type="checkbox" name="permission[{$role.id}][{$act.id}]"
									{if (!empty($permissions[$role.key][$act.module_url][$act.action]) && $permissions[$role.key][$act.module_url][$act.action] == 'enable') || (empty($permissions[$role.key][$act.module_url][$act.action]) && ($role['default_permission'] == 'enable' || $act.admin == 0))} checked="checked"{/if}
								/>
							</td>
						{/foreach}
					</tr>
				{/if}
			{/foreach}
		{/if}
	{/if}
</table>
*}