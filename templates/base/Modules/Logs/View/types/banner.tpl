{?$entity_rus = array(
    'баннер',
    'баннера',
    'баннеру'
)}
{if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Создан {else}Удален {/if}
        {$entity_rus.0} <strong>
        {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{else}ID:{$l.entity_id}{/if}
        </strong>
{elseif $l.type == 'edit'}
    Изменен параметр у {$entity_rus.1} {if !empty($l.additional_data.title)}«{$l.additional_data.title}»{else}ID:{$l.entity_id}{/if}<br />
    {$l.attr_id}: {$l.comment}
{else}
    ???
{/if}