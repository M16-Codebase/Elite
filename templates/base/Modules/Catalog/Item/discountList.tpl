{if !empty($discounts)}
    {foreach from=$discounts item=dc name="discount_list"}
        <tr>
            <td class="td-title">{iteration}</td>
            <td>{$dc.title}</td>
            <td>{if !empty($dc.segment_id) && !empty($segments[$dc.segment_id])}{$segments[$dc.segment_id]['title']}{elseif empty($dc.segment_id)}Все{else}Не определен{/if}</td>
            <td>{date('d.m.Y', strtotime($dc.date_start))}</td>
            <td>{date('d.m.Y', strtotime($dc.date_end))}</td>
            <td class="td-center"><div class="table-btn delete" data-discount-id="{$dc.id}"></div></td>{* /catalog-item/delDiscount/  POST['id'] (товара) POST['discount_id'] *}
        </tr>
    {/foreach}
{/if}