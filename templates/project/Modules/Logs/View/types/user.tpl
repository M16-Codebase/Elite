{if $l.type == 'create' || $l.type == 'delete'}
        {if $l.type == 'create'}Создан {else}Удален {/if}
        пользователь <strong>
        {if !empty($l.additional_data.email)}«{$l.additional_data.email}»{/if} 
        </strong>{if !empty($l.additional_data.role)} с ролью «{$l.additional_data.role}»{/if}
        {if !empty($l.additional_data.region_id)} в регионе {$l.additional_data.region_id}{/if}
{elseif $l.type == 'edit'}
    {if !empty($l.user_entity)}
        {?$user_email = $l.user_entity->getEmail()}
        У пользователя <strong>{if !empty($user_email)}«{$user_email}»{/if}</strong>
        изменен параметр «{$l.attr_id}» на:
        {$l.comment}
    {/if}
{else}
    ???
{/if}