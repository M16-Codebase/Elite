{?$a_data = $l.additional_data}
{if is_array($a_data.t)}
    [Баг, починен 09.09.2015 13:49]
{elseif array_key_exists('v', $a_data) && is_array($a_data.v)}
    [Баг, починен 09.09.2015 17:54]
{else}
    {if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Добавлен {else}Удален {/if}
        файл <strong>
        «{if !empty($a_data.t)}{$a_data.t}{elseif !empty($a_data.f_n)}{$a_data.f_n}{else}{$l.entity_id}{/if}» 
        </strong>
    {elseif $l.type == 'edit'}
        {if $l.attr_id == 'file'}
            Файл <strong>«{$a_data.t}»</strong> изменен
        {else}
            У файла <strong>«{$a_data.t}»</strong>
            изменен параметр «{if !empty($logged_fields[$l.attr_id])}{$logged_fields[$l.attr_id]}{else}{$l.attr_id}{/if}» на:
            {$a_data.v}
        {/if}
    {else}
        ???
    {/if}
{/if}