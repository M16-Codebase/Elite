

{if !empty($text)}

<div id="district_seo_text" class="main-sc post article">
{if isset($text['title'])}
    <h2 class="descr">{$text['title']}</h2>
{/if}

{if isset($text['annotation'])}
    <div class="annotation">{$text['annotation']|html}</div>
{/if}

{if isset($text['text'])}
        <div class="article-text">{$text['text']|html}</div>
{/if}
</div>

{/if}
