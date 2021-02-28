{if !empty($catalog_item['video'])}
    {foreach from=$catalog_item['video'] item=vidos name="video_list"}
        <tr>
            <td class="td-title">{iteration}</td>
            <td><input type="text" name="video[{$vidos.id}][title]" /></td>
            <td><input type="text" name="video[{$vidos.id}][description]" /></td>
            <td><input type="text" name="video[{$vidos.id}][url]" /></td>
            <td class="td-center"><div class="delete" data-video_id="{$vidos.id}"></div></td>
        </tr>
    {/foreach}
{/if}
<tr class="new-video">
	<td class="td-title">{count($catalog_item['video']) + 1}</td>
	<td><input type="text" name="video[0][title]" /></td>
	<td><input type="text" name="video[0][description]" /></td>
	<td><input type="text" name="video[0][url]" /></td>
	<td class="td-center"</td>
</tr>