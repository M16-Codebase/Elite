{?$subject = 'Заказ подтвержден'}
{?$paymentMail = 1}
{include file="mails/order_sent.tpl"}
{*<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Рады сообщить, что заказ {$order.id} подтвержден</font></h1>
<div style="font-size: 12px; color: #67727e;">Благодарим за то, что Вы выбрали <a href="http://{$site_url}" style="font-size: 12px; text-decoration: none; font-style: italic; color: #666666;">Мастер Сантехник</a>!</div>
<div style="margin-top: 30px;">
    <h2 style="color: #000000; font-size: 18px; text-transform: uppercase; font-weight: normal;"><font color="black">ЧТО ДАЛЬШЕ?</font></h2>
    <div style="color: #000000; font-size: 16px;"><font color="black">Оплата{if $order.pay_type == 'nal' && $order.delivery_type_courier == 1} и доставка{/if}</font></div>
    {include file="components/paytype_text.tpl"}
    <p style="color: #67727e; font-size: 12px; font-style: italic; margin: 10px 0px 20px 0px;">
        {if $order.pay_type == 'nal' && $order.delivery_type_courier == 1}
            Вы выбрали оплату наличными курьеру. В день доставки курьер свяжется с вами для подтверждения времени. При получении товара не забудьте проверить комплектность заказа и получить чек об оплате у курьера.
        {else}
            Вы выбрали для оплаты заказа {!empty($paytype_text[$order.pay_type]) ? $paytype_text[$order.pay_type] : ''}.{if $order.pay_type != 'nal'} Для оплаты перейдите по ссылке<br />ниже и на странице заказа на сайте нажмите кнопку &laquo;Оплатить заказ&raquo;{/if}
        {/if}
    </p>
</div>
{if $order->canPayed() && !empty($order_view_link)}
    <div style="margin-top: 20px;">
        <a href="{$order_view_link}" style="font-size: 16px; color: #0a5488;">Получить ссылку на оплату товара &rarr;</a>
    </div>
{/if}
{include file="mails/orderList.tpl"}
*}