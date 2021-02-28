{if !empty($discounts)}
    {foreach from=$discounts item=dc}
        <tr data-id="{$dc.id}">
			<td class="small">
				<input type="hidden" name="id" value="{$dc.id}" />
				{if $account->isPermission('discount', 'move')}
					<input type="hidden" name="position" value="{$dc.position}" />
					<div class="drag-drop m-active"></div>
				{/if}
			</td>
			<td class="small">{$dc.id}</td>
            <td><a href="/discount/edit/?id={$dc.id}">{if !empty($dc.title)}{$dc.title}{else}Акция №{$dc.id}{/if}</a></td>
            <td>{$dc.status_rus}</td>
            <td>{if !empty($dc.segment_id) && !empty($segments[$dc.segment_id])}{$segments[$dc.segment_id]['title']}{elseif empty($dc.segment_id)}Все{else}Не определен{/if}</td>
            <td>{if !empty($dc.date_start)}{date('d.m.Y', strtotime($dc.date_start))}{/if}</td>
			<td>{if !empty($dc.date_end)}{date('d.m.Y', strtotime($dc.date_end))}{/if}</td>
            <td class="small td-center"><div class="table-btn delete delete-discount"></div></td>
        </tr>
    {/foreach}
{/if}