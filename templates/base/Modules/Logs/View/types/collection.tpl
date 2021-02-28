{?$a_data = $l.additional_data}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создана {else}Удалена {/if}
    фотогалерея <strong>
    {if !empty($a_data.t)}«{$a_data.t}»{else}ID: {$l.entity_id}{/if} 
    </strong>
{elseif $l.type == 'edit'}
    {if !empty($l.attr_id)}
        У фотогалереи <strong>{if !empty($a_data.t)}«{$a_data.t}»{else}ID: {$l.entity_id}{/if}</strong>
        изменен параметр «{$l.attr_id}» на:
        {$a_data.v}
    {/if}
{elseif $l.type == 'images'}
    {if $a_data.a == 'create'}Загружено {elseif $a_data.a == 'delete'}Удалено {else}Изменено {/if}
    изображение <strong>ID:{$l.comment}</strong>
    у фотогалереи <strong>{if !empty($a_data.t)}«{$a_data.t}»{else}ID: {$l.entity_id}{/if}</strong>
{else}
    ???
{/if}