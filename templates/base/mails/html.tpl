<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>LPS</title>
</head>
<body style="height:100%;background-color:#f4f4f4;margin:0;padding:0;border:0;">
<table  cellspacing="0" cellpadding="0" width="100%" border="0" style="font-family:Arial; background-color:#f4f4f4;">
	<tr>
		<td style="height:41px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	</tr>
	<tr>
		<td align="center">
			<table cellspacing="0" cellpadding="0" border="0">
				<tr background='http://{$site_url}/templates/base/img/mails/bg-sides{if !empty($bigSize)}-order{/if}.png' style="background-repeat:repeat-y;">
					<td align="center">	
						<table cellspacing="0" cellpadding="0" border="0" width="{if !empty($bigSize)}835{else}735{/if}" style="font-family:Arial; padding-left:4px; padding-right:4px;">
							<tr>
								<td style="background-color:#fff;border-top:1px solid #dcdcdc; border-bottom:1px solid #ccc; border-left:1px solid #ccc; border-right:1px solid #ccc;text-align:left; padding-top:28px; padding-right:34px; padding-bottom:28px; padding-left:43px;">
									{if !empty($site_config.site_logo)}<img src="http://{$site_url}{$site_config.site_logo->getUrl(200,200)}" alt="{if !empty($site_config.project_name)}{$site_config.project_name}{/if}" style="max-width:400px">{/if}
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
			<table width='{if !empty($bigSize)}835{else}735{/if}' cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td background='http://{$site_url}/templates/base/img/mails/bg-bottom{if !empty($bigSize)}-order{/if}.png' height='6' style=" background-repeat:no-repeat;">
						<img src="http://{$site_url}/templates/base/img/mails/space.gif" alt="">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="height:41px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
	</tr>
	{$mail_content|html}
	{include file="mails/mail_bottom.tpl"}
</table>
</body>
</html>
{*<html>

<head>
</head>

<body>
    {if empty($site_url)}
        {?$site_url = $smarty.server.SERVER_NAME}
    {/if}
	<div style="background: #f2f3f7; font-family: Arial; padding-top: 33px; padding-left: 100px; padding-right: 100px; padding-bottom: 150px;">
		<div style="background: #ffc508; width: 790px; padding-top: 25px; padding-left: 18px; padding-right: 18; padding-bottom: 23px;">
			<a href="http://{$site_url}"><img src="http://{$site_url}/templates/img/icons/header-logo.png" /></a>
		</div>
		<div style="width: 790px;">
			{$mail_content|html}
		</div>
	</div>
</body>
</html>
*}