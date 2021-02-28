{? $pageTitle='Вспомогательные сео-ссылки'}


<div class="tabs-cont main-tabs">
    <div class="content-top">
        <h1>{$module_info['title']}</h1>
    </div>

</div>
<div id="tabs-pages" class="content-scroll-cont">
    <div id="items" class="tab-page actions-cont m-current">
        {include file='Modules/Seo/SupportSeoLinks/list.tpl'}
    </div>
</div>

{include file='Modules/Seo/SupportSeoLinks/dialog.tpl'}
