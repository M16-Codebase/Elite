{if !empty($task.data.type_id)}Изменили в id: <a href="/catalog-type/?id={$task.data.type_id}" title="{$task.data.type_title}" target='_blank'>{$task.data.type_id}</a><br />{/if}
{if isset($task.data.items_count)}Товаров: {$task.data.items_count}<br />{/if}
{if isset($task.data.variants_count)}Вариантов: {$task.data.variants_count}<br />{/if}
{if !empty($task.data.properties_values)}Значения: <br/>
    {foreach from=$task.data.properties_values key=prop_id item=val}
        {$prop_id} - {is_array($val) ? implode(', ', $val) : $val}
    {/foreach}
{/if}