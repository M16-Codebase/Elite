{if !empty($bottom)}
<tr>
	<td style="" align="center">
		<table cellspacing="0" width='736px' cellpadding="0" border="0" style="font-family:Arial; padding-top:38px;  padding-bottom:22px;">
			<tr>
				<td style="text-align:left; padding-left:45px;">
					<span style='font-size:15px; color:#606f82'>Это письмо сгенерировано автоматически. Пожалуйста, не отвечайте на него.</span><br>
					{if !empty($site_config.contact_mail)}<span style='font-size:15px; color:#99a1a8'>Жалобы и предложения по работе сервиса просим направлять по адресу: <a href="mailto:{$site_config.contact_mail}" style='color:#0060ff; text-decoration:none;font-size:15px;'>{$site_config.contact_mail}</a></span>{/if}
				</td>
			</tr>
		</table>
	</td>
</tr>
{/if}
