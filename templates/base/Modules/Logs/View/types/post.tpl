{include file='Modules/Logs/View/post_types_rus.tpl'}
{?$a_data = $l.additional_data}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создан{if $a_data.t_t != 'texts'}а{/if} {else}Удален{if $a_data.t_t != 'texts'}а{/if} {/if}
    {$types_rus[$a_data.t_t][0]} <strong>
        «{$a_data.t}»</strong>{if !empty($a_data.t_th)} в теме «{$a_data.t_th}»{/if}
{elseif $l.type == 'edit'}
    У {$types_rus[$a_data.t_t][1]} <strong>«{if !empty($a_data.t)}{$a_data.t}{else}{$l.entity_id}{/if}»</strong>
    изменен параметр «{$logged_fields[$l.attr_id]}» на:
    {$a_data.v}
{else}
    ???
{/if}