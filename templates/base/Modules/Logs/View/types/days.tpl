{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создан {else}Удален {/if}
    выходной день <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
{elseif $l.type == 'edit'}
    Установлены выходные дни:
        {$l.comment}
{/if}