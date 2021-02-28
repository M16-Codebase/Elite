{?$subject = 'Заказ оплачен'}
{?$admin_mail = 1}
{?$payedAdminMail = 1}
{include file="mails/order_sent.tpl"}
{*<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Заказ {$order.id} оплачен.</font>
</div>
<div style="margin-top: 20px;">
	<div style="color: #666666; font: italic 12px/14px Arial; margin-top: 9px;">
		{if !empty($post)}
			{$post.text|html}
		{/if}
	</div>
</div>
{include file="mails/orderList.tpl" order_total=false}
{include file="mails/mail_bottom.tpl" bottom=1}*}