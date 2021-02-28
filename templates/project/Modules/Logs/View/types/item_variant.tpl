{?$a_data = $l.additional_data}
{if !empty($a_data) && !empty($catalogs[$l['catalog_id']])}
	{?$catalog_words = $catalogs[$l['catalog_id']]['word_cases']}
	{if $l.type == 'create' || $l.type == 'delete'}
		{if $l.entity_type == 'item'}
			{if $l.type == 'create'}Создан {else}Удален {/if}
			{$catalogs[$l['catalog_id']]['word_cases']['i'][1]['i']}<strong>
			«{$a_data.t}»
			</strong>
		{elseif $l.entity_type == 'variant'}
			{if $l.type == 'create'}Создан {else}Удален {/if}
			{$catalog_words['v'][1]['i']} <strong>«{$a_data.t}»</strong>
			в {$catalog_words['i'][1]['d']} <strong>«{$a_data.i_t}»</strong>
		{/if}
		в категории <strong>«{$a_data.t_t}»</strong>
    {elseif $l.type == 'attr'}
        {include file="Modules/Logs/View/types/attr.tpl"}
	{elseif $l.type == 'transfer_item'}
		{$catalogs[$l['catalog_id']]['word_cases']['i'][1]['i']} {if !empty($a_data.t)}<strong>«{$a_data.t}»</strong>{else}ID:{$l.entity_id}{/if}
		перенесен из типа <a href="/catalog-type/catalog/?id={$a_data.old_type_id}">{$a_data.old_type_title}</a>
		в <a href="/catalog-type/catalog/?id={$a_data.new_type_id}">{$a_data.new_type_title}</a>
	{elseif $l.type == 'transfer_variant'}
		{$catalogs[$l['catalog_id']]['word_cases']['v'][1]['i']} {if !empty($a_data.t)}<strong>«{$a_data.t}»</strong>{else}ID:{$l.entity_id}{/if}
		перенесен из {$catalogs[$l['catalog_id']]['word_cases']['i'][1]['r']} <a href="/catalog-view/?id={$a_data.old_item_id}">{$a_data.old_item_title}</a>
		в <a href="/catalog-view/?id={$a_data.new_item_id}">{$a_data.new_item_title}</a>
	{else}
		???
	{/if}
{else}
    {if empty($a_data)}
        [Неизвестное событие]
    {else}
        [Событие с удаленным каталогом ID {$l['catalog_id']}]
    {/if}
{/if}
