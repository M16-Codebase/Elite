{$catalog_type.title}

{if !empty($items)}
    Объекты с мета-тегами:
    <ul>
        {foreach from=$items item=item}
            <li>{$item.title}</li>
        {/foreach}
    </ul>
{/if}