{if !empty($bottom)}
<tr>
	<td align="center">
		<table cellspacing="0" width='{if !empty($bigSize)}841{else}736{/if}' cellpadding="0" border="0" style="font-family:Arial; padding-top:38px;  padding-bottom:22px;">
			<tr>
				{if !empty($bigSize)}
				<td style="padding-left:40px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				{/if}
				<td style="text-align:left; padding-left:{if !empty($bigSize)}7{else}45{/if}px;">
					<span style='font-size:15px; color:#606f82'>Это письмо сгенерировано автоматически. Пожалуйста, не отвечайте на него.</span><br>
					{if !empty($site_config.claim_email)}<span style='font-size:15px; color:#99a1a8'>Жалобы и предложения по работе сервиса просим направлять по адресу: <a href="mailto:{if empty($admin_mail)}{$site_config.claim_email}{else}info@webactives.ru{/if}" style='color:#059fdb; text-decoration:none;font-size:15px;'>{if empty($admin_mail)}{$site_config.claim_email}{else}info@webactives.ru{/if}</a></span>{/if}
				</td>
				{if !empty($bigSize)}
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				{/if}
			</tr>
		</table>
	</td>{?$admin_mail = 1}
</tr>
<tr>
	<td style="height:41px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
</tr>
{/if}
{*<div style="font-family: Arial; color: #000000">
	<div style="color: #000000">
		<div style="font-size: 16px; color: #000000; margin-top: 39px;"><font color="black">Если у вас есть вопросы, свяжитесь с нами</font></div>
		{if !empty($region)}
			<table style="width: 310px; margin-top: 16px;">
				<tr>
					{if !empty($region.phone)}
						<td style="color: #666666; font-size: 12px;">Телефон</td>
					{/if}
					{if !empty($region.email)}
						<td style="color: #666666; font-size: 12px;">Электронная почта</td>
					{/if}	
				</tr>
				<tr>
					{if !empty($region.phone)}
						<td style="font-size: 16px;  color: #000000; line-height: 22px;"><font color="black">{$region.phone}</font></td>
					{/if}
					{if !empty($region.email)}
						<td><a style="font-size: 16px; line-height: 22px; color: #005b9f; text-decoration: none;" href="mailto:{$region.email}">{$region.email}</a></td>
					{/if}
				</tr>
			</table>
		{/if}
	</div>
	{if !empty($bottom)}
		<div style="color: #666666; font-size: 12px; margin-top: 29px;">С уважением,<br /><span style="color: #666666; font-style: italic; font-size: 12px;">Администрация <a href="http://{$site_url}" style="text-decoration: none; font-size: 12px; font-style: italic; color: #666666;">«Мастер Сантехник»</a></span></div>
		<div style="border-bottom: 1px solid #e7e7ea; color: #666666; font-size: 12px; margin-top: 33px; padding-bottom: 70px; text-transform: uppercase;">
			Это письмо сгенерировано автоматически. Пожалуйста, не отвечайте на него.
            {if !empty($site_config.service)}Вопросы вы можете задать, 
                воспользовавашись любым из способов, указанных выше. Жалобы и предложения к службе сервиса просим
                направлять по адресу: <a style="color: #005b9f; text-decoration: none;" href="mailto:{$site_config.service}">{$site_config.service}</a>
            {/if}
		</div>
	{/if}
</div>*}