{if !empty($file_types)}
    {foreach from=$file_types key=t_id item=f_type}
        {if !empty($files[$t_id])}
            <tr data-file-id="{$files[$t_id]['id']}">
                <td><a href="{$files[$t_id]['link']}" target="_blank">{$files[$t_id]['title']}</a></td>
                <td>{$f_type}</td>
                <td class="small">
					<div class="table-btn delete" title="Удалить файл"></div>
				</td>{* /catalog-item/delFile/ POST['id'] - id варианта POST['file_id']*}
            </tr>
        {/if}
    {/foreach}
{/if}