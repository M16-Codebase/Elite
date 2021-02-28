{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создан {else}Удален {/if}
    опрос <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
{elseif $l.type == 'edit'}
    У опроса <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
    изменен параметр «{$l.attr_id}» на:
    {$l.comment}
{else}
    ???
{/if}