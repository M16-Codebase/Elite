<hr />
<ul>
    {foreach from=$items item=gallery}
        <li>
            {?$cover = $gallery.images->getCover()}
            <a href="{$gallery->getUrl()}">
                {if !empty($cover)}<img src="{$cover->getUrl(100,100)}"><br />{/if}
                {$gallery.title}
            </a>
        </li>
    {/foreach}
</ul>

<hr />

{include file="components/paging.tpl"}