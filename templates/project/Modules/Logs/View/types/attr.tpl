{?$a_data = $l.additional_data}
{if !empty($catalogs[$l['catalog_id']])}
{?$catalog_words = $catalogs[$l['catalog_id']]['word_cases']}
Свойство <strong>{$a_data.at_n}</strong> 
{if $l['entity_type'] == 'variant' && !empty($l.variant)}
    {$catalog_words['v'][1]['r']} <strong>{$a_data.t}</strong> в {$catalog_words['i'][1]['d']} <strong>{$a_data.i_t}</strong>
{else}
    <span style="text-transform: lowercase;">{$catalog_words['i'][1]['r']}</span>
    {if !empty($a_data.p)}{**список родительских айтемов с названиями категорий*}
        {?$parents = $a_data.p + array($a_data.t_t => $a_data.t)}
        {?$p_str = array()}
        {foreach from=$parents item=p_title key=pt_title}{?$p_str[] = $pt_title . ' — <strong>' . $p_title . '</strong>'}{/foreach}
        {implode(', ', $p_str)|html}
    {else}
        <strong>{$a_data.t}</strong>
        категории <strong>{$a_data.t_t}</strong>
    {/if}
{/if} изменено:
<div class="cols-cont justify">
    <div class="log-col">
        {if isset($a_data.at_is_ent) && $a_data.at_is_ent}{*сообщаем о том, что изменилось свойство-объект*}
            <a href="/logs-view/?type={if $a_data.at_dt == 'gallery'}collection{else}{$a_data.at_dt}{/if}&entity_id={$a_data.v}" target="_blank" title="Открыть все изменения объекта">{if !empty($a_data.at_t)}{$a_data.at_t}{else}{$a_data.v}{/if}</a>
        {else}
            {if !isset($a_data.v)}
                Удалено значение <div class="cols-cont">{$a_data.o_v_r}</div>
            {elseif !isset($a_data.o_v)}
                Добавлено значение <div class="cols-cont">{$a_data.v_r}</div>
            {else}
                {if $a_data.at_s}Значение {$a_data.o_v_r} на {/if}
                <div class="cols-cont">{$a_data.v_r}</div>
            {/if}
        {/if}
    </div>
</div>
{else}
    [Событие с удаленным каталогом ID {$l['catalog_id']}]
{/if}