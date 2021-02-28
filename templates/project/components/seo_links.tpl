

{if isset($seoLinks) && (isset($seoLinks['static'])
    && count($seoLinks['static']) || isset($seoLinks['dinamyc']) && count($seoLinks['dinamyc']))}

    {if isset($seoLinks['static']) && count($seoLinks['static'])
        && isset($seoLinks['dinamyc']) && count($seoLinks['dinamyc'])}
        {? $width = 31}
        {else}
        {? $width = 45}
    {/if}

    <div id="seoLinksList" class="main-sc post article">
        <h2 class="descr">{$lang->get('Наши объекты', 'Our objects')}</h2>

    {if isset($seoLinks['static']) && count($seoLinks['static'])}
        <ul class="seoLinks" style="width:{$width}%;padding-left:1em;">
            {foreach from=$seoLinks['static'] item=link}
                <li><a href="{$link['href']}">{$link['text']}</a></li>
            {/foreach}
        </ul>
    {/if}
    {if isset($seoLinks['dinamyc']) && count($seoLinks['dinamyc'])}

            {? $left = array()}
            {? $right = array()}
            {? $l_counter = 1}
            {foreach from=$seoLinks['dinamyc'] item=link}
                {if $l_counter % 2 != 0}
                    {? $left[] = $link}
                {else}
                    {? $right[] = $link}
                {/if}
                {? $l_counter ++}
            {/foreach}

            <ul class="seoLinks left" style="width:{$width}%;padding-left:1em;">
                {foreach from=$left item=link}
                    <li><a href="{$link['href']}">{$link['text']}</a></li>
                {/foreach}
            </ul>
            <ul class="seoLinks right" style="width:{$width}%;padding-left:1em;">
                {foreach from=$right item=link}
                    <li><a href="{$link['href']}">{$link['text']}</a></li>
                {/foreach}
            </ul>
    {/if}
    </div>
{/if}
