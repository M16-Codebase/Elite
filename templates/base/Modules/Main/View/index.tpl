{*{include file="components/social-auth.tpl"}*}

{*?$menu_items = $infoBlocks->get('menu', 'menu')}
<ul>
{foreach from=$menu_items item=item}
    <li>
        <a href="{$item.url}" title="{$item.title}">{$item.name}{if !empty($item.image)}<img src="{$item.image->getUrl()}">{/if}</a>
        {if $item.has_children}
            <ul>
                {foreach from=$item.child_items item=child_item}
                    <li>
                        <a href="{$child_item.url}" title="{$child_item.title}">{$child_item.name}{if !empty($child_item.image)}<img src="{$child_item.image->getUrl()}">{/if}</a>
                    </li>
                {/foreach}
            </ul>
        {/if}
    </li>
{/foreach}
</ul>*}

{*<form id="autocomplete-form" method="post" action="/main/autocomplete/">
    <input type="text" name="search">
    <input type="submit">
</form>

<div id="autocomplete-result"></div>*}
<h1>{$h1 = 'Главная страница'}</h1>