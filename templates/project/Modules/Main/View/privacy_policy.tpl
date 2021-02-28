{if $request_segment.key == 'ru'}
    {?$pageTitle = $page_posts.privacy_policy.title}
{else}
    {?$pageTitle = 'Privacy policy'}
{/if}
<div class="main-sc post article">
    <h2 class="descr">{$page_posts.privacy_policy.title}</h2>

    <div class="article-text">
        {$page_posts.privacy_policy.text|html}
    </div>
</div>