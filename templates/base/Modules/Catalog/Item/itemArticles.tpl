{if !empty($attached_articles_types)}
    {foreach from=$attached_articles_types item=art_type_rus key=art_type}
        <tr class="at-{$art_type}">
            <td class="param_title">
				<a href="{if empty($catalog_item['attaches'][$art_type])}
				    /catalog-item/createAttachPost/?item_id={$catalog_item.id}&attach_type={$art_type}
				{else}
					/attach/edit/?id={$catalog_item['attaches'][$art_type]['id']}&item_id={$catalog_item.id}
				{/if}">{$art_type_rus}</a>
			</td>
			<td class="visability">

			</td>
			<td class="site">
				{if !empty($catalog_item['inheritable_attaches'][$art_type])}
					{?$current_art_type = ($art_type == "installation")? "introduce" : $art_type}
					<a href="/catalog/viewItem/{$catalog_item.id}/{$current_art_type}/" title="Перейти на сайт"></a>
				{/if}
			</td>
            <td class="del">
				{if !empty($catalog_item['attaches'][$art_type])}
                    <a href="#" title="Удалить"
                       data-art_id="{$catalog_item['attaches'][$art_type]['id']}"
                       data-art_type="{$art_type}"
                       class="delete_article del_button_round_small">
                    </a>
               {/if}
           </td>
        </tr>
    {/foreach}
{/if}