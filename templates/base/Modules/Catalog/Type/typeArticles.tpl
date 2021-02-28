{if !empty($attached_articles_types)}
    {foreach from=$attached_articles_types item=art_type_rus key=art_type}
        <tr>
            <td class="td-title">
				<a href="{if empty($current_type['attaches'][$art_type])}
				    /catalog-type/createAttachPost/?type_id={$current_type.id}&attach_type={$art_type}
				{else}
					/attach/edit/?id={$current_type['attaches'][$art_type]['id']}&type_id={$current_type.id}
				{/if}">{$art_type_rus}</a>
				{if !empty($current_type['attaches'][$art_type]) && $current_type['attaches'][$art_type]['status'] == 'new'} (Черновик){/if}
			</td>
           <td class="td-center">
			   <div class="visibility"></div>
			</td>
			<td class="td-center">
				{if !empty($current_type['inheritable_attaches'][$art_type])}
					<a class="site" href="#" title="Перейти на сайт"></a>
				{/if}
			</td>
            <td class="td-center">
				{if !empty($current_type['attaches'][$art_type])}
                    <div class="delete" title="Удалить" data-art_type="{$art_type}"></fiv>
               {/if}
           </td>
        </tr>
    {/foreach}
{/if}