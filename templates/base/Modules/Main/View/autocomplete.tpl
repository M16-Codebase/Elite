{if !empty($searches.items)}
    <h4>Товары</h4>
    <ul>
        {foreach from=$searches.items item=item}
            <li>
                {$item.title}
            </li>
        {/foreach}
    </ul>
{/if}
{if !empty($searches.posts)}
    <h4>Статьи</h4>
    <ul>
        {foreach from=$searches.posts item=post}
            <li>
                {$post.title}
            </li>
        {/foreach}
    </ul>
{/if}