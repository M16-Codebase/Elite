{if !empty($task.data.type_id)}Выгрузили из id: <a href="/catalog-type/?id={$task.data.type_id}" title="{$task.data.type_title}" target='_blank'>{$task.data.type_id}</a><br />{/if}
{if isset($task.data.items_count)}Товаров: {$task.data.items_count}<br />{/if}
{if isset($task.data.variants_count)}Вариантов: {$task.data.variants_count}{/if}