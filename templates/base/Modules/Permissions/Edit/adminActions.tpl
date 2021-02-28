<table class="ribbed">
    {if !empty($roles)}
        <tr>
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
                        <tr>
                            <td>/{$act.module_url}/*/</td>
                            <td>{if !empty($default_access_rights[$module_url])}{$default_access_rights[$module_url]|var_dump}{/if}</td>
                            {foreach from=$roles item=role}
                                <td>
                                    <input type="checkbox"/>
                                </td>
                            {/foreach}
                        </tr>
                    {/if}
                    <tr>
                        <td>/{$act.module_url}/{$act.action}/</td>
                        <td>{$act.title}</td>
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