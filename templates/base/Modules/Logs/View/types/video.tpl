{if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Создано {else}Удалено {/if}
        видео <strong>
        {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if} 
        </strong> ID {$l.entity_id}&nbsp;
        {*if count($l.additional_data) > 2*}{*id и title*}
        {*с параметрами:
            {foreach from=$l.additional_data item=value key=field}
                {if $field != 'title' && $field != 'id'}
                    {$field}: {$value}<br />
                {/if}
            {/foreach}
        {/if*}
{elseif $l.type == 'edit'}
    {if !empty($l.attr_id)}
        В видео <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
        изменен параметр «{$l.attr_id}» на:<br />
        {$l.comment}
    {/if}
{elseif $l.type == 'assoc'}
    {if $l.additional_data.changes_type == 'add'}
        К видео <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
        прикреплен объект ID #{$l.comment}
    {elseif $l.additional_data.changes_type == 'delete'}
        От видео <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
        откреплен объект ID #{$l.comment}
    {else}
        ???
    {/if}
{else}
    ???
{/if}