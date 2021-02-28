{?$admin_mail = 1}
{include file="mails/status_change.tpl"}
{*Текущее состояние заказа номер {$order.number}
{include file="mails/orderList.tpl" order_total=true}*}