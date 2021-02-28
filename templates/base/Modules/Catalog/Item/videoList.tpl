{if !empty($videos)}
    {foreach from=$videos item=vidos name="video_list"}
        <tr>
            <td class="td-title">{iteration}</td>
            <td>{$vidos.title}</td>
            <td>{$vidos.description}</td>
            <td>{$vidos.url}</td>
            <td class="td-center"><div class="table-btn delete" data-video-id="{$vidos.id}"></div></td>{* /catalog-item/delVideo/ POST['id'] (товара) POST['video_id'] *}
        </tr>
    {/foreach}
{/if}