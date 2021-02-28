{if (!empty($task.data.items_count)) || !empty($task.data.variants_count)}
    Пересчитаны свойства 
    {if isset($task.data.items_count)}
        {$task.data.items_count|plural_form:'товар':'товара':'товаров'}
    {/if}
    {if isset($task.data.variants_count)}
        {if isset($task.data.items_count)}, {/if}{$task.data.variants_count|plural_form:'вариант':'варианта':'вариантов'}
    {/if}
{/if}