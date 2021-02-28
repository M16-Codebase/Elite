{?$subject = 'Заказ оплачен'}
{?$payedMail = 1}
{include file="mails/order_sent.tpl"}
{*{?$payed_mail = 1}
{?$positions = $order.positions}
<tr>
	<td align="center">
		<table cellspacing="0" cellpadding="0" border="0" width='735px' style="">
			<tr background='http://{$site_url}/templates/base/img/mails/bg-sides.png' style="background-repeat:repeat-y;">
				<td align="center" style="padding-left:4px; padding-right:4px;">	
					<table cellspacing="0" cellpadding="0" width="727" border="0" style="border-top:1px solid #dcdcdc;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-family:Arial; background-color:#fff;">
						<thead>
							<tr>
								<th style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></th>
								<th style="text-align:left; font-size:30px; font-weight:bold; text-transform:uppercase; line-height:31px; padding-top:25px; padding-left:23px; padding-bottom:10px;">
									Заказ {if !empty($order.id)}№{$order.id}{/if} оплачен
								</th>
								<th style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></th>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
								<td style="border-bottom:3px solid #059fdb;">
									<img src="http://{$site_url}/templates/base/img/mails/space.gif" alt="">
								</td>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
								<td style="padding-top:14px; padding-bottom:33px; text-align:left; padding-left:23px; padding-right:0px;">*}
{*									<span style="font-weight:bold;font-size:18px;letter-spacing:-0.02em;">Мы храним Ваш пароль в зашифрованном виде, поэтому восстановить доступ можно только задав новый пароль.</span><br><br>*}
{*									<span style="font-size:15px;color:#606f82;">Для формирования нового пароля перейдите по ссылке<br><a href="http://google.com" style="font-size:15px;font-weight:bold;color:#059fdb;text-decoration:none; line-height:25px;">https://docs.google.com/spresheet/c?key=0AjJPhT14564564561341PEF6cEpNVVNzMG5</a></span>*}
{*								</td>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>*}
{*<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Здравствуйте{if !empty($user.name)}, {$user.name}{/if}!</font>
</div>
<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Заказ {$order.id} оплачен. Спасибо!</font>
</div>
<div style="font-size: 12px; font-style: italic; color: #666666;">Благодарим за то, что Вы выбрали <a href="http://{$site_url}" style="text-decoration: none; font-size: 12px; font-style: italic; color: #666666;">Мастер Сантехник</a>!</div>
<div style="margin-top: 21px;">
	<a href="/profile/orders/" style="font-size: 16px; color: #005b9f; text-decoration: none;">Отслеживайте статус заказа в личном кабинете на сайте &rarr;</a>
</div>
<div style="margin-top: 20px;">
	<span style="color: #000000; font-size: 18px; margin-bottom: 10px;"><font color="black">Что дальше?</font></span>
	<div style="color: #000000; font-size: 16px; margin-top: 16px;"><font color="black">Получаете покупку</font></div>
	<div style="color: #666666; font: italic 12px/14px Arial; margin-top: 9px;">
		{if !empty($post)}
			{$post.text|html}
		{/if}
	</div>
</div>
{include file="mails/orderList.tpl" order_total=false}*}
{include file="mails/mail_bottom.tpl" bottom=1}






{*<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Заказ {$order.id} оплачен. Спасибо!</font></h1>
<div style="font-size: 12px; color: #67727e;">Благодарим за то, что Вы выбрали Apex Sport!</div>
{if !empty($order_view_link)}
    <div style="margin-top: 20px;">
        <a href="{$order_view_link}" style="font-size: 16px; color: #0a5488;">Отслеживайте статус заказа на сайте &rarr;</a>
    </div>
{/if}
<div style="margin-top: 30px;">
    <h2 style="color: black; font-size: 18px; text-transform: uppercase; font-weight: normal;"><font color="black">ЧТО ДАЛЬШЕ?</font></h2>
    <div style="color: black; font-size: 16px;"><font color="black">Получаете покупку</font></div>
    <p style="color: #67727e; font-size: 12px; font-style: italic; margin: 10px 0px 20px 0px;">
        {if !empty($post)}
            {$post.text|html}
        {/if}
    </p>
</div>
{include file="mails/orderList.tpl"}
{?$has_q = 1*}