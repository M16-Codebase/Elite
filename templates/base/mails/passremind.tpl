{?$subject = 'Восстановление пароля'}
<tr>
	<td align="center">
		<table cellspacing="0" cellpadding="0" border="0" width='735px' style="">
			<tr background='http://box.webactives.ru/letterTesting/lps/bg-sides.png' style="background-repeat:repeat-y;">
				<td align="center" style="padding-left:4px; padding-right:4px;">	
					<table cellspacing="0" cellpadding="0" width="727" border="0" style="border-top:1px solid #dcdcdc;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-family:Arial; background-color:#fff;">
						<thead>
							<tr>
								<th style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></th>
								<th style="text-align:left; font-size:30px; font-weight:bold; text-transform:uppercase; line-height:31px; padding-top:25px; padding-left:23px; padding-bottom:18px;">
								восстановление доступа
								</th>
								<th style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></th>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
								<td style="border-bottom:3px solid #059fdb;">
									<img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt="">
								</td>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
								<td style="padding-top:14px; padding-bottom:33px; text-align:left; padding-left:23px; padding-right:0px;">
									<span style="font-weight:bold;font-size:18px;letter-spacing:-0.02em;">Мы храним Ваш пароль в зашифрованном виде, поэтому восстановить доступ можно только задав новый пароль.</span><br><br>
									<span style="font-size:15px;color:#606f82;">Для формирования нового пароля перейдите по ссылке<br><a href="http://{$site_url}/welcome/newpass/?check={$hash}" style="font-size:15px;font-weight:bold;color:#059fdb;text-decoration:none; line-height:25px;">http://{$site_url}/welcome/newpass/?check={$hash}</a></span>
								</td>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
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
				<td width="735" background='http://{$site_url}/templates/base/img/mails/bg-bottom.png' height='6' style=" background-repeat:no-repeat;">
					<img src="http://{$site_url}/templates/base/img/mails/space.gif" alt="">
				</td>
			</tr>
		</table>
	</td>
</tr>
{include file="mails/mail_bottom.tpl" bottom=1}


{*Здравствуйте{if !empty($user_info.name)}, {$user_info.name}{else}.{/if}<br>
Вы или кто-то другой запросили новый пароль на этот емайл. <br>
Если вам нужно восстановить ваш пароль на сайте <a href="http://{$site_url}">{$site_url}</a>, то пройдите по приведенной ниже ссылке:<br>
 <a href="http://{$site_url}/welcome/newpass/?check={$hash}">http://{$site_url}/welcome/newpass/?check={$hash}</a>,
в противном случае просто пригнорируйте данное письмо.*}
