{foreach from=$catalog_items item=order}
    <div class="order-cont white-body order-{$order.id}">
        {include file="Modules/Order/Admin/order.tpl"}
    </div>
{/foreach}