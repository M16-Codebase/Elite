{?$a_data = $l.additional_data}
{?$min_max_step = array(
    'min' => 'Минимальное значение',
    'max' => 'Максимальное значение',
    'step' => 'Шаг'
)}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создано {else}Удалено {/if}
    свойство <strong>
    «{$a_data.t}»
    </strong> в {if $a_data.t_is_c}каталоге{else}категории{/if} <strong>«{$a_data.t_t}»</strong>
{elseif $l.type == 'edit'}
    В свойстве <strong>«{$a_data.t}»</strong>
    {if $a_data.t_is_c}каталога{else}категории{/if} <strong>«{$a_data.t_t}»</strong>
    изменен параметр <strong>«{$logged_fields[$l.attr_id]}»</strong>:<br />
    {?$dt = empty($a_data.dt) ? $a_data.d_t : $a_data.dt}
    {if $dt == 'enum' && $l['attr_id'] == 'values'}
        {if $l.comment == 'add'}
            Добавлено значение: {$a_data.v}
        {elseif $l.comment == 'edit'}
            Изменено значение с {$a_data.o_v} на {$a_data.v}
        {elseif $l.comment == 'delete'}
            Удалено значение: {$a_data.v}
        {else}???{/if}
    {else}
        {if is_array($a_data.v)}
            {foreach from=$a_data.v item=val key=num}
                {if isset($properties_key[$l.attr_id][$val])}
                    {$properties_key[$l.attr_id][$val]}
                {else}
                    {if ($dt == 'int' || $dt == 'float') && $l['attr_id'] == 'values'}
                        {$min_max_step.$num} &mdash;
                    {/if}
                    {$val}
                {/if}<br />
            {/foreach}
        {else}
            {if isset($properties_key[$l.attr_id][$a_data.v])}
                {$properties_key[$l.attr_id][$a_data.v]}
            {else}
                {$a_data.v}
            {/if}
        {/if}
    {/if}
{else}
    ???
{/if}