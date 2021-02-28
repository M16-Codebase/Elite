{if isset($page_posts.top_content)}
    {?$cover = $page_posts.top_content.gallery->getCover()}
    <div class="container container-spacer">
    <div class="row text-center">
    <div class="col align-self-center"><img class="top-100"{if !empty($cover)}src="{$cover->getUrl()}"{/if}></div>
    </div>
    <div class="row justify-content-center text-justify">
    <div class="col-sm-10">
    {$page_posts.top_content.text|html}
    <p class="sub">{$page_posts.top_content.annotation}</p>
    </div>
    </div>
    </div>
{/if}