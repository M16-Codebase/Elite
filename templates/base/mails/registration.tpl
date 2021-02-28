{?$subject = "Вы зарегистрированы в системе"}
<tr>
	<td align="center">
		<table cellspacing="0" cellpadding="0" border="0" width='735' style="">
			<tr background='http://box.webactives.ru/letterTesting/lps/bg-sides.png' style="background-repeat:repeat-y;">
				<td align="center" style="padding-left:4px; padding-right:4px;">	
					<table cellspacing="0" cellpadding="0" width="727" border="0" style="border-top:1px solid #dcdcdc; border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; font-family:Arial; background-color:#fff;">
						<thead>
							<tr>
								<th style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></th>
								<th colspan="2" style=" text-align:left; font-size:30px; font-weight:bold; text-transform:uppercase; line-height:31px; padding-top:25px; padding-left:23px; padding-bottom:18px;">
									Вы зарегистрированы<br>на сайте <a href="http://{$site_url}" style="color:#000000; text-decoration:none;">{$site_url}</a>
								</th>
								<th style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></th>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
								<td colspan="2" style="border-bottom:3px solid #059fdb;">
									<img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt="">
								</td>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
								<td style="line-height:0; width:57px; padding-top:18px; padding-left:24px; padding-right:18px;">
									<img src="http://box.webactives.ru/letterTesting/lps/access-man.png" alt="" width='42' height='52'>
								</td>
								<td style="padding-top:14px; text-align:left;">
									<span style="font-weight:bold;font-size:18px;">Спасибо за регистрацию!</span><br>
									<span style="font-size:15px;color:#606f82;">Данные доступа в Ваш личный кабинет указаны ниже.</span>
								</td>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
							</tr>
							<tr>
								<td style="padding-left:18px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
								<td colspan="2" style="padding-top:20px; padding-bottom:20px">
									<table style="background-color:#f3f3f3;" width="100%" cellspacing="0" cellpadding="0" border="0">
										<tr><td style="padding-top:19px;line-height:0;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td></tr>
										<tr>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #d2d8e1; width:268px;">
												Страница авторизации
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1; padding-bottom:7px; padding-top:4px;">
												<a href="http://{$site_url}/login/" style="font-weight:bold;color:#059fdb;text-decoration:none;">{$site_url}/login/</a>
											</td>
											<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
										</tr>
										{if !empty($user_email)}
										<tr>
											<td style="padding-left:22px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; border-bottom:1px solid #d2d8e1;width:268px;">
												Логин (электронная почта)
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #d2d8e1;  padding-bottom:7px; padding-top:7px;">
												<a href="mailto:{$user_email}" style="color:#000000; text-decoration:none;">{$user_email}</a>
											</td>
											<td style="padding-left:22px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
										</tr>
										{/if}
										{if !empty($new_pass)}
										<tr>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
											<td style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; width:268px; padding-bottom:25px;" valign="top">
												Пароль
											</td>
											<td style="font-size:15px; color:#000000; text-align:left; padding-top:6px; padding-bottom:25px;">
												{$new_pass}
											</td>
											<td style="padding-left:22px; padding-bottom:25px;"><img src="http://box.webactives.ru/letterTesting/lps/space.gif" alt=""></td>
										</tr>
										{/if}
									</table>
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


{*
Здравствуйте!<br />
Вы зарегистрированы на сайте <a href="http://{$site_url}">{$site_url}</a>.<br />
Ссылка для входа в систему администрирования: <a href="http://{$site_url}/login/">http://{$site_url}/login/</a><br />
Ваш логин: <b>{$user_email}</b><br />
Ваш пароль: <b>{$new_pass}</b><br /> *}