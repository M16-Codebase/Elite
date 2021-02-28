{?$subject = 'Новый заказ ' . $order.id}
{?$positions = $order.positions}
{?$admin_mail = 1}
{include file="mails/order_sent.tpl"}

{*<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Вам поступил заказ</font>
</div>
<div style="margin-top: 25px; background-color: #e5e5e5; padding: 13px;">
	<span style="font-size: 18px; color: #666666;">Номер заказа &mdash; <span style="color: #000000; font-weight: bold;">{$order.id}</span></span>
</div>
<div style="margin-top: 21px;">
	<a href="http://{$site_url}/order-admin/?id={$order.id}"style="font-size: 16px; color: #005b9f; text-decoration: none;">Открыть заказ в системе &rarr;</a>
</div>
{include file="mails/orderList.tpl" order_total=true}


<table border="0" cellspacing="0" cellpadding="0" style="margin-top:30px; background: #ffffff; border: 1px solid #dcdcdc; font-family: Arial; margin-top: 30px; margin-left: 3px; width: 477px;">
	<tr>
		<td colspan="2" style="text-align: left; font-size: 18px; padding-left: 20px; padding-top: 26px; padding-bottom: 10px;">Контактные данные</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">Фамилия</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($order.surname)}{$order.surname}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">Имя</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($order.name)}{$order.name}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">E-mail</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($order.email)}<a  style="color: #005b9f; text-decoration: none;" href="mailto:{$order.email}" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; color:#06449B; text-decoration:none">{$order.email}</a>{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">Телефон</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 6px; padding-bottom: 5px; padding-left: 20px; padding-right: 20px;">{if !empty($order.phone)}{$order.phone}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">Организация</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold;  border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($order.company_name)}{$order.company_name}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-left: 20px; padding-top: 15px; padding-bottom: 15px; width: 95px;">ИНН</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold;  border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($order.inn)}{$order.inn}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; padding-top: 15px; padding-left: 20px; padding-bottom: 35px; width: 95px;">Комментарии</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; padding-top: 12px; padding-bottom: 35px; padding-left: 20px; padding-right: 20px;">{if !empty($order.descr)}{$order.descr}{else}&nbsp;{/if}</td>
	</tr>
</table>

{?$admin_mail = 1}
*}

{*<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Вам поступил заказ</font></h1>
<div style="margin-top: 25px; background-color: #e7e7ea; padding: 15px;">
    <span style="font-size: 18px; color: #8e8f9d; text-transform: uppercase;">Номер заказа &mdash; <span style="color: #d42050;">{$order.id}</span></span>
</div>
<div style="margin-top: 20px;">
    <a href="http://{$site_url}/order-admin/?id={$order.id}" style="font-size: 16px; color: #0a5488;">Открыть заказ в системе &rarr;</a>
</div>

{?$admin_mail = 1}
{include file="mails/orderList.tpl"}*}