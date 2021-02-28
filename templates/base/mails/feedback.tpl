{?$subject = 'Обращение с формы обратной связи' . (!empty($feedback_number) ? ' №'.$feedback_number : '')}
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
								Обращение с формы<br>обратной связи{(!empty($feedback_number) ? ' №'.$feedback_number : '')}
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
												Фамилия 
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												{if !empty($surname)}{$surname}{else}—{/if}
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
										<tr>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #d2d8e1; width:268px;">
												Имя, Отчество
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												{if !empty($name) || !empty($patronymic)}{if !empty($name)}{$name}{/if}{if !empty($patronymic)}{$patronymic}{/if}{else}—{/if}
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
										<tr>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #d2d8e1; width:268px;">
												Электронная почта
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												{if !empty($mail)}<a href="mailto:{$mail}" style="color:#000;text-decoration:none;">{$mail}</a>{else}—{/if}
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
										<tr>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #d2d8e1; width:268px;">
												Телефон
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												{if !empty($phone)}{$phone}{else}—{/if}
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
										</tr>
										<tr>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; width:268px; padding-bottom:25px;" valign="top">
												Комментарии
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; padding-top:6px; padding-bottom:25px;">
												{if !empty($text)}{$text}{else}—{/if}
											</td>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
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

{*<div style="margin-top: 20px;">
	<div style="color: #666; font-size: 12px; font-style: italic; line-height: 14px; font-family: Arial; margin-top: 9px;">
		{$text}
	</div>
	<div style="font-size: 12px; color: #000; margin-top: 15px;">{$name} ({$phone_email})</div>
</div>*}
		
{*<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Вопрос с сайта</font></h1>
<div style="margin-top: 30px; background-color: #ffffff; padding: 30px 30px;">
    <div style="font-size:14px;color:#67727e">
        {$text}
    </div>
    <div style="font-size: 14px; font-style: italic; margin-top: 10px;"><font color="black">{$name} ({$phone_email})</font></div>
</div>*}

{*{assign var="subject" value="Обратная связь"}
<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 30px;">Обратная связь</div>
<table style="background: #ffffff; border: 1px solid #dcdcdc; font-family: Arial; font-size: 14px; margin-top: 30px; width: 477px;">
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-top: 30px; padding-bottom: 15px; padding-left: 20px; width: 95px;">Фамилия, имя</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 30px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($name)}{$name}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; width: 95px;">E-mail</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 16px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($email)}<a href="mailto:{$email}" style="font-family:Arial; font-size:14px; color:#06449B; text-decoration:none">{$email}</a>{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; border-bottom: 1px solid #eceeed; padding-top: 15px; padding-bottom: 15px; padding-left: 20px; width: 95px;">Телефон</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; border-bottom: 1px solid #eceeed; padding-top: 16px; padding-bottom: 15px; padding-left: 20px; padding-right: 20px;">{if !empty($phone)}{$phone}{else}&nbsp;{/if}</td>
	</tr>
	<tr>
		<td style="font-size: 14px; color: #000000; font-family: Arial; padding-top: 15px; padding-bottom: 30px; padding-left: 20px; vertical-align: top; width: 95px;">Сообщение</td>
		<td style="font-size: 14px; color: #000000; font-family: Arial; font-weight: bold; padding-top: 16px; padding-bottom: 30px; padding-left: 20px; padding-right: 20px;">{if !empty($text)}{$text}{else}&nbsp;{/if}</td>
	</tr>
</table>
*}
		
{*
<div style="margin-top: 20px;">
	<div style="color: #666; font-size: 12px; font-style: italic; line-height: 14px; font-family: Arial; margin-top: 9px;">
		{$text}
	</div>
	<div style="font-size: 12px; color: #000; margin-top: 15px;">{$name} ({$phone_email})</div>
</div>
*}		
{*<h1 style="font-size: 18px; color: #000000; font-weight: bold; text-transform: uppercase; margin-top: 0px; margin-bottom: 5px;"><font color="black">Вопрос с сайта</font></h1>
<div style="margin-top: 30px; background-color: #ffffff; padding: 30px 30px;">
    <div style="font-size:14px;color:#67727e">
        {$text}
    </div>
    <div style="font-size: 14px; font-style: italic; margin-top: 10px;"><font color="black">{$name} ({$phone_email})</font></div>
</div>*}