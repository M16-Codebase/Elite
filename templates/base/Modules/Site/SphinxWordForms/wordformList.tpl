{if !empty($wordforms)}
    {foreach from=$wordforms item=wf}
        {include file="Modules/Site/SphinxWordForms/wordformGroup.tpl" wordform=$wf}
    {/foreach}
{elseif !empty($smarty.get.search)}
    <li class='nothing-found'>По вашему запросу ничего не найдено</li>
{else}
    <li class='nothing-exists'>Синонимов пока нет</li>
{/if}