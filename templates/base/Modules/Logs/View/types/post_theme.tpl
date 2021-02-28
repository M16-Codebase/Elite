{include file='Modules/Logs/View/post_types_rus.tpl'}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создана {else}Удалена {/if}
    тема для {if !empty($types_rus[$l.additional_data.type][2])}{$types_rus[$l.additional_data.type][2]}{else}"default"{/if} <strong>
        {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
{elseif $l.type == 'edit'}
    У темы для {if !empty($types_rus[$l.additional_data.type][2])}{$types_rus[$l.additional_data.type][2]}{else}"default"{/if}<strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
    изменен параметр «{$l.attr_id}» на:
    {$l.comment}
{else}
    ???
{/if}