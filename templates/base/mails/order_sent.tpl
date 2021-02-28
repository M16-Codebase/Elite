{?$subject = 'Заказ поступил в обработку'}
{?$bigSize=1}
{?$positions = $order.positions}
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0" width='835' style="">
				<tr background='http://{$site_url}/templates/base/img/mails/bg-sides-order.png' style="background-repeat:repeat-y;">
					<td align="center" style="padding-left:4px; padding-right:4px;">	
						<table cellspacing="0" cellpadding="0" width="827" border="0" style="border-top:1px solid #dcdcdc; border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-family:Arial; background-color:#fff;">
							<thead>
								<tr>
									<th style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></th>
									<th colspan="2" style=" text-align:left; font-size:30px; font-weight:bold; text-transform:uppercase; line-height:31px; padding-top:30px; padding-left:20px; padding-bottom:11px;">
										{if !empty($admin_mail)}Вам поступил заказ №{if !empty($order.id)}{$order.id}{/if}
										{elseif !empty($statusChangeMail)}
											Заказ {if !empty($order.id)}№{$order.id}{/if}:<br>
											статус изменен на «{if !empty($order.state)}{$order.state}{/if}»
										{elseif !empty($payedAdminMail) || !empty($payedMail)}
											Заказ {if !empty($order.id)}№{$order.id}{/if} оплачен
										{elseif !empty($paymentMail)}
											Заказ {if !empty($order.id)}№{$order.id}{/if}:<br>
											cпособ оплаты изменен на <span style="white-space:nowrap;">«{if !empty($order.pay_type)}{$order.pay_type}{/if}»</span>
										{else}
											Заказ {if !empty($order.id)}№{$order.id}{/if}<br>поступил в обработку
										{/if}
									</th>
									<th style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></th>
								</tr>
								<tr>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
									<td colspan="2" style="border-bottom:3px solid #059fdb;">
										<img src="http://{$site_url}/templates/base/img/mails/space.gif" alt="">
									</td>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
									<td style="padding-left:20px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
									<td style="padding-top:28px; text-align:left;">
										<span style="font-weight:bold;font-size:18px;">{$positions|@count|plural_form:'позиция':'позиции':'позиций'}  на сумму <span style="white-space:nowrap;">{$order.positions_price|price_format} руб.</span></span><br>
										<span style="font-size:15px;color:#606f82;">
											{if !empty($admin_mail)}
												<a href="http://{$site_url}/order-admin/" target="_blank" style="font-weight:bold;color:#059fdb;text-decoration:none;">Открыть заказ в системе</a>
											{else}
											Заказ также доступен в <a href="http://{$site_url}/order-admin/" target="_blank" style="font-weight:bold;color:#059fdb;text-decoration:none;">истории заказов</a> в личном кабинете
											{/if}
										</span>
									</td>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
								</tr>
								<tr>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
									<td colspan="2" style="padding-top:20px; padding-bottom:20px">
										<table style="" width="100%" cellspacing="0" cellpadding="0" border="0">
											<tr><td style="padding-top:1px;line-height:0;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td></tr>
											{include file="mails/orderList.tpl" order_total=true}
										</table>
									</td>
									<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td width="835" background='http://{$site_url}/templates/base/img/mails/bg-bottom-order.png' height='6' style=" background-repeat:no-repeat;">
						<img src="http://{$site_url}/templates/base/img/mails/space.gif" alt="">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	{include file="mails/orderContacts.tpl"}
{*{?$subject = 'Заказ поступил в обработку'}
<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Здравствуйте{if !empty($order.name)}, {$order.name}{/if}!</font>
</div>
<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Ваш заказ поступил в обработку</font>
</div>
<div style="font-size: 12px; font-style: italic; color: #666666;">Благодарим за то, что Вы выбрали <a href="http://{$site_url}" style="font-size: 12px; text-decoration: none; font-style: italic; color: #666666;">Мастер Сантехник</a>!</div>
<div style="margin-top: 25px; background-color: #e5e5e5; padding: 13px;">
	<span style="font-size: 18px; color: #666666;">Номер заказа &mdash; <span style="color: #000000; font-weight: bold;">{$order.id}</span></span>
</div>
<div style="margin-top: 21px;">
	<a href="http://{$site_url}/profile/orders/" style="font-size: 16px; color: #005b9f; text-decoration: none;">Отслеживайте статус заказа в личном кабинете на сайте &rarr;</a>
</div>
<div style="margin-top: 44px;">
	<span style="font-size: 18px; color: #000000; margin-bottom: 10px;"><font color="black">Что дальше?</font></span>
	<div style="font-size: 16px; color: #000000; margin-top: 16px;"><font color="black">Менеджер позвонит для подтверждения</font></div>
	<div style="color: #666666; font: italic 12px/14px Arial; margin-top: 9px;">Дождитесь звонка оператора нашего интернет-магазина.<br />
		Он согласует и подтвердит ваш заказ, а также объяснит как вы сможете его получить.</div>
</div>*}
{*{include file="mails/orderList.tpl" order_total=true}*}
{include file="mails/mail_bottom.tpl" bottom=1}





{*?$subject = 'Заказ поступил в обработку'}
<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Отлично! Ваш заказ поступил в обработку</font></h1>
<div style="font-size: 12px; color: #67727e;">Благодарим за то, что Вы выбрали Apex Sport!</div>
<div style="margin-top: 25px; background-color: #e7e7ea; padding: 15px;">
    <span style="font-size: 18px; color: #8e8f9d; text-transform: uppercase;">Номер заказа &mdash; <span style="color: #d42050;">{$order.id}</span></span>
</div>
<div style="margin-top: 20px;">
    <a href="{$order_view_link}" style="font-size: 16px; color: #0a5488;">Отслеживайте статус заказа на сайте &rarr;</a>
</div>
<div style="margin-top: 30px;">
    <h2 style="color: #000000; font-size: 18px; text-transform: uppercase; font-weight: normal;"><font color="black">ЧТО ДАЛЬШЕ?</font></h2>
    <div style="color: #000000; font-size: 16px;"><font color="black">Менеджер позвонит для подтверждения</font></div>
    <p style="color: #67727e; font-size: 12px; font-style: italic; margin: 10px 0px 20px 0px;">Сотрудник магазина подтвердит наличие товара, оговарит детали доставки<br />и оплаты. Колл-центр работает ежедневно <span style="text-transform: lowercase;">{$site_config['phone_time']}</span>.</p>
    <div style="color: #000000; font-size: 16px;"><font color="black">Вы получаете электронное письмо</font></div>
    <p style="color: #67727e; font-size: 12px; font-style: italic; margin: 10px 0px 20px 0px;">Оно содержит инструкцию по оплате и получению заказа.</p>
    <div style="color: #000000; font-size: 16px;"><font color="black">Оплачиваете и получаете покупку</font></div>
    <p style="color: #67727e; font-size: 12px; font-style: italic; margin: 10px 0px 20px 0px;">Вы платите наличными в банке, салоне связи, на почте, в терминале или<br />совершаете платеж онлайн. После поступления платежа Вы получаете<br />свой заказ.</p>
</div>
{include file="mails/orderList.tpl"}
{?$has_q = 1*}