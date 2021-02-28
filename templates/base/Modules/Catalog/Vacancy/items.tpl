<hr />
<ul>
    {foreach from=$items item=item}
        <li>
            <a href="{$item->getUrl()}">
                <h4>{$item.title}</h4>
                <p>{$item.anouncement}</p>
            </a>
        </li>
    {/foreach}
</ul>

<hr />

{include file="components/paging.tpl"}