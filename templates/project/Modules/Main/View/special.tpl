{if $request_segment.key == 'ru'}
    {?$pageTitle = 'Акции и Спецпредложения | М16-Недвижимость'}
    {?$pageDescription = 'Актуальные акции и спецпредложения на покупку и продажу элитной недвижимости в Санкт-Петербурге'}
{else}
    {?$pageTitle = 'Actions & Special offers | M16 Real Estate Agency'}
    {?$pageDescription = 'Actual promotions and special offers of elite estate sale and buying in St.Petersburg'}
{/if}
{if !empty($page_posts.gift) && $page_posts.gift.status == 'close'}
    <h4>{$page_posts.gift.title}</h4>
    <p>{$page_posts.gift.annotation}</p>
    <div>{$page_posts.gift.text|html}</div>
{/if}

{include file='Modules/Main/View/specialItems.tpl' items=$gift count=$gift_count}

{if !empty($page_posts.discount) && $page_posts.discount.status == 'close'}
    <h4>{$page_posts.discount.title}</h4>
    <p>{$page_posts.discount.annotation}</p>
    <div>{$page_posts.discount.text|html}</div>
{/if}

{include file='Modules/Main/View/specialItems.tpl' items=$discount count=$discount_count}