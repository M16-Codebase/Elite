<h1>{$brand.title}</h1>

{if $brand->isMonoBrand()}
    <h2>{$item.title}</h2>
{else}
    {foreach from=$items item=item}
        <h3>{$item.title}</h3>
    {/foreach}
{/if}