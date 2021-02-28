<!DOCTYPE html>
<html lang="en" style="height:100%;">
<head>
	<meta charset="UTF-8">
	<title>LPS</title>
</head>
<body style="height:100%;background-color:#f4f4f4;margin:0;padding:0;border:0;">
<table  cellspacing="0" cellpadding="0" width="100%"  border="0" style="font-family:Arial; background-color:#f4f4f4;">
	<tr>
		<td style="height:41px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
	</tr>
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0" width='735' style="">
				<tr background='http://{$site_url}/templates/project/img/mails/bg-sides.png' style="background-repeat:repeat-y;">
					<td align="center">	
						<table cellspacing="0" cellpadding="0" border="0" width="735" style="font-family:Arial; padding-left:4px; padding-right:4px;">
							<tr>
								<td style="border-top:1px solid #dcdcdc; border-bottom:1px solid #ccc; border-left:1px solid #ccc; border-right:1px solid #ccc;text-align:left; padding-top:18px; padding-right:34px; padding-bottom:21px; padding-left:45px; background-color:#fff;">
									{if !empty($site_config.site_logo)}<img src="http://{$site_url}{$site_config.site_logo->getUrl(210)}" alt="{if !empty($site_config.project_name)}{$site_config.project_name}{/if}" style="max-width:400px">{/if}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			<table width='735px' cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td background='http://{$site_url}/templates/project/img/mails/bg-bottom.png' height='6' style=" background-repeat:no-repeat;">
						<img src="http://{$site_url}/templates/project/img/mails/space.gif" alt="">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="height:41px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
	</tr>
	{$mail_content|html}
	<tr>
		<td style="height:41px;"><img src="http://{$site_url}/templates/project/img/mails/space.gif" alt=""></td>
	</tr>
</table>
</body>
</html>

