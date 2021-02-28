{foreach from=$catalog_item.galleries item=collection}
    {if !empty($collection)}
    <tr data-collection_id="{$collection->getId()}" data-position="{$collection->getPosition()}">
        <td><div class="drag-drop"></div></td>
        <td class="td-title">
            {$collection->getColorText()}
        </td>
    </tr>
    {/if}
{/foreach}