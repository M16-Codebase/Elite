{?$subject = 'Заказ звонка' . (!empty($feedback_number) ? ' №'.$feedback_number : '')}
<tr>
	<td align="center">
		<table cellspacing="0" cellpadding="0" border="0" width='735px' style="">
			<tr background='http://{$site_url}/templates/project/img/mails/bg-sides.png' style="background-repeat:repeat-y;">
				<td align="center" style="padding-left:4px; padding-right:4px;">	
					<table cellspacing="0" cellpadding="0" width="727" border="0" style="border-top:1px solid #dcdcdc; border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-family:Arial; background-color:#fff;">
						<thead>
							<tr>
								<th style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></th>
								<th colspan="2" style=" text-align:left; font-size:30px; font-weight:bold; text-transform:uppercase; line-height:31px; padding-top:25px; padding-left:23px; padding-bottom:18px;">
								Обращение с формы<br>заказа звонка{(!empty($feedback_number) ? ' №'.$feedback_number : '')}
								</th>
								<th style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></th>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
								<td colspan="2" style="border-bottom:3px solid #059fdb;">
									<img src="http://{$site_url}/templates/project/img/mails/space.gif" alt="">
								</td>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
								<td style="line-height:0; width:57px; padding-top:18px; padding-left:24px; padding-right:18px;">
									<img src="http://{$site_url}/templates/project/img/mails/new-message.png" alt="" width='57' height='50'>
								</td>
								<td style="padding-top:14px; text-align:left;">
									<span style="font-weight:bold;font-size:18px;">Вам поступило новое обращение</span><br>
									<span style="font-size:15px;color:#606f82;">Оно также доступно в <a href="http://{$site_url}/feedback/" target="_blank" style="font-weight:bold;color:#059fdb;text-decoration:none;">базе обращений</a> в административной части сайта</span>
								</td>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
								<td colspan="2" style="padding-top:20px; padding-bottom:20px">
									<table style="background-color:#f3f3f3;" width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr><td style="padding-top:19px;line-height:0;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td></tr>
										<tr>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #d2d8e1; width:268px;">
												Имя 
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												{if !empty($name)}{$name}{else}—{/if}
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
										<tr>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; width:268px; padding-bottom:25px;" valign="top">
												Телефон
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; padding-top:6px; padding-bottom:25px;">
												{if !empty($phone)}{$phone}{else}—{/if}
											</td>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
									</table>
								</td>
								<td style="padding-left:18px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
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
				<td background='http://{$site_url}/templates/project/img/mails/bg-bottom.png' height='6' style=" background-repeat:no-repeat;padding-left:368px;padding-right:368px;">
					<img src="http://{$site_url}/templates/project/img/mails/space.gif" alt="">
				</td>
			</tr>
		</table>
	</td>
</tr>
{include file="mails/mail_bottom.tpl" bottom=1}	

{*
<h4>Контактное лицо</h4>
<p>
    {$name}
</p>
{if !empty($organisation)}
<h4>Организация</h4>
<p>
    {$organisation}
</p>
{/if}
{if !empty($position)}
<h4>Должность</h4>
<p>
    {$position}
</p>
{/if}
<h4>Телефон</h4>
<p>
    {$phone}
</p>
<h4>Электронная почта</h4>
<p>
    {$email}
</p>
<h4>Удобное время звонка</h4>
<p>
    {$time_from}-{$time_to}
</p>
<h4>Тема обращения</h4>
<p>
    {$issue}
</p>
{if !empty($message)}
<h4>Сообщение</h4>
<p>
    {$message}
</p>
{/if}
	*}