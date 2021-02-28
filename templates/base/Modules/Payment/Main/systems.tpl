{?$pageTitle ='Платежные системы — Управление сайтом | ТехноАльт'}
{?$admin_page = 1}
<h1>Платежные системы</h1>
<table class="ribbed">
    <thead>
        <tr>
            <th>Ключ</th>
            <th>Название</th>
            <th>Используется</th>
            <th>Процент</th>
            <th>Группа</th>
        </tr>
    </thead>
    <tbody class="systems-list">
        {if !empty($pay_methods)}
			{?$group_title = -1}
            {foreach from=$pay_methods item=method}
                <tr data-key="{$method.key}">
				{*
                    при изменении галки, отправляем 0 или 1 на /payConfirm/setSystemUsed/ POST key = $method.key, used = 1|0
                    при изменении группы /payConfirm/setSystemGroup/ key = $method.key, group_id = $gr.id
                *}
                    <td>{$method.key}</td>
                    <td>{$method.name}</td>
                    <td>
						<input type="checkbox" class="change-used" value="1"{if $method.used} checked{/if} />
					</td>
					<td>{$method.commission_project}%</td>
                    <td>
						<select class="change-group">
							<option value="0">Группа не задана</option>
							{if !empty($groups)}
								{foreach from=$groups item=gr}
									<option value="{$gr.id}"{if $gr.id == $method.group_id} selected{/if}>{$gr.title}</option>
								{/foreach}
							{/if}
						</select>
					</td>
                </tr>
            {/foreach}
        {/if}
    </tbody>
</table>