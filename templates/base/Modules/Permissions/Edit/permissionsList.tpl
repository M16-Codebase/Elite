<ul id="tabs">
    {?$req_tab = !empty($smarty.get.tab)? $smarty.get.tab : 'admin'}
    <li><a data-tab="admin" href="?tab=admin" class="{if $req_tab == 'admin'}m-current{/if}">Адимнские</a></li>
    <li><a data-tab="public" href="?tab=public" class="{if $req_tab == 'public'}m-current{/if}">Публичные</a></li>
</ul>
<div id="tabs-pages">
    <div id="admin" class="tab-page{if $req_tab == 'admin'} m-current{/if}">
        {include file="Modules/Permissions/Edit/actionsTabContent.tpl" actions=$actions_admin}
    </div>
    <div id="public"class="tab-page{if $req_tab == 'public'} m-current{/if}">
        {include file="Modules/Permissions/Edit/actionsTabContent.tpl" actions=$actions_public}
    </div>
</div>