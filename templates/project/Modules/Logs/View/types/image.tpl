{?$a_data = $l.additional_data}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Добавлено {else}Удалено {/if}
    изображение <strong>
    «{if !empty($a_data.f_n)}{$a_data.f_n}{else}{$l.entity_id}{/if}» 
    </strong>
{elseif $l.type == 'edit'}
    {if $l.attr_id == 'file'}
        Изображение <strong>«{$a_data.f_n}»</strong> изменено на:
        {$a_data.w} x {$a_data.h}
    {else}
        У изображения <strong>«{$a_data.f_n}»</strong>
        изменен параметр «{if !empty($logged_fields[$l.attr_id])}{$logged_fields[$l.attr_id]}{else}{$l.attr_id}{/if}» на:
        {$a_data.v}
    {/if}
{else}
    ???
{/if}