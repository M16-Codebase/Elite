{if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Создана {else}Удалена {/if}
        категория видео <strong>
        {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if} 
        </strong> ID {$l.entity_id}
        {if count($l.additional_data) > 2}{*id и title*}
        с параметрами:
            {foreach from=$l.additional_data item=value key=field}
                {if $field != 'title' && $field != 'id'}
                    {$field}: {$value}<br />
                {/if}
            {/foreach}
        {/if}
{elseif $l.type == 'edit'}
    {if !empty($l.attr_id)}
        В категории видео <strong>{if !empty($l.additional_data.title)}«{$l.additional_data.title}»{/if}</strong>
        изменен параметр «{$l.attr_id}» на:
        {$l.comment}
    {/if}
{else}
    ???
{/if}