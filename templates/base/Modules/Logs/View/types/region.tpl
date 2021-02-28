{if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Создан {else}Удален {/if}
        регион <strong>
        {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if} 
        </strong> ID {$l.entity_id}
{elseif $l.type == 'edit'}
    {if !empty($l.attr_id)}
        В регионе <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
        изменен параметр «{$l.attr_id}» на:
        {$l.comment}
    {/if}
{else}
    ???
{/if}